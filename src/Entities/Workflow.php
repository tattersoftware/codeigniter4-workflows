<?php

namespace Tatter\Workflows\Entities;

use Config\Services;
use OutOfBoundsException;
use Tatter\Users\Interfaces\HasPermission;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Models\ExplicitModel;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;

class Workflow extends BaseEntity
{
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Default set of attributes.
     */
    protected $attributes = [
        'role' => '',
    ];

    /**
     * Array store of Stages initialized as nodes (e.g. see Stage::getNext())
     * and indexed by the Stage IDs.
     *
     * @var array<int,Stage>|null
     */
    protected ?array $stages = null;

    /**
     * Builds and returns the indexed node tree.
     *
     * @return array<int,Stage>
     */
    public function getStages(): array
    {
        $this->ensureCreated();

        if ($this->stages === null) {
            $stages = model(StageModel::class)->
                where('workflow_id', $this->attributes['id'])
                    ->orderBy('id', 'asc')
                    ->findAll();

            $previous     = null;
            $current      = null;
            $this->stages = [];

            while ($current = array_shift($stages)) {
                if ($previous !== null) {
                    $previous->setNext($current);
                }

                $current->setPrevious($previous);
                $this->stages[$current->id] = $current;

                $previous = $current;
            }
        }

        return $this->stages;
    }

    /**
     * Returns the Stage from the node tree matching the ID.
     *
     * @throws OutOfBoundsException
     */
    public function getStageById(int $stageId): Stage
    {
        if (! array_key_exists($stageId, $this->getStages())) {
            throw new OutOfBoundsException('Workflow ' . $this->attributes['id'] . ' does not contain stage ' . $stageId);
        }

        return $this->getStages()[$stageId];
    }

    /**
     * Returns the first Stage from the node tree matching the Action ID.
     * Note that repeated Actions may make using this method seem buggy
     *
     * @throws WorkflowsException
     */
    public function getStageByAction(string $actionId): Stage
    {
        foreach ($this->getStages() as $stage) {
            if ($stage->action_id === $actionId) {
                return $stage;
            }
        }

        throw new WorkflowsException($this->attributes['name'] . ' does not contain action ' . $actionId);
    }

    /**
     * Checks if a role filter is set, and if a user (defaults to current)
     * has that permission to access this Workflow.
     *
     * @param array<int,bool>|null $explicits An array of explicit associations from
     *                                        users_workflows. Mostly injected so when
     *                                        checking many Workflows at once to prevent
     *                                        duplicate database calls
     */
    public function mayAccess(?HasPermission $user = null, ?array $explicits = null): bool
    {
        $this->ensureCreated();

        // If no user was provided then try for the current user
        if ($user === null && $userId = user_id()) {
            /** @var HasPermission|null $user */
            $user = Services::users()->findById($userId);
        }

        // Check explicits first
        if ($user !== null) {
            if ($explicits === null) {
                $explicit = model(ExplicitModel::class)
                    ->where('user_id', $user->getId())
                    ->where('workflow_id', $this->attributes['id'])
                    ->first();

                if ($explicit !== null) {
                    return (bool) $explicit->permitted;
                }
            } elseif (array_key_exists($this->attributes['id'], $explicits)) {
                return $explicits[$this->attributes['id']];
            }
        }

        // Anyone else is allowed unrestricted Workflows
        if ($this->attributes['role'] === '') {
            return true;
        }

        // If still no user then deny
        if ($user === null) {
            return false;
        }

        return $user->hasPermission($this->attributes['role']);
    }

    /**
     * Progresses a Job to the next Stage in this Workflow.
     *
     * @throws WorkflowsException
     */
    public function progress(Job $job): void
    {
        $this->step($job, 'up');
    }

    /**
     * Progresses a Job to the next Stage in this Workflow.
     *
     * @throws WorkflowsException
     */
    public function regress(Job $job): void
    {
        $this->step($job, 'down');
    }

    /**
     * Progresses a Job to the next Stage in this Workflow.
     *
     * @param 'down'|'up' $method
     *
     * @throws WorkflowsException
     */
    private function step(Job $job, string $method): void
    {
        if ($job->getStage() === null) {
            throw new WorkflowsException(lang('Workflows.jobAlreadyComplete'));
        }

        // Get the appropriate Stage
        $stage = $method === 'up'
            ? $job->getStage()->getNext()
            : $job->getStage()->getPrevious();

        if ($stage !== null) {
            $job->stage_id = $stage->id;

            // Trigger the appropriate Action event
            $action = $stage->getAction();
            $job    = $action::$method($job);
        }
        // No next stage means the job is complete
        elseif ($method === 'up') {
            $job->stage_id = null;
        }
        // Do not allow regressing before the first Stage
        else {
            throw new WorkflowsException(lang('Workflows.jobCannotRegress'));
        }

        // If all went well then update the Job
        model(JobModel::class)->save($job);
    }

    /**
     * Moves a Job through the Workflow skipping non-required Stages
     * but running their Action functions.
     *
     * @throws WorkflowsException
     */
    public function travel(Job $job, Stage $target, bool $checkRequired = true): void
    {
        // Check for an easy out
        $current = $job->getStage();
        if ($current->id === $target->id) {
            return;
        }

        // Determine the direction of travel
        if ($current->id < $target->id) {
            if ($checkRequired) {
                // Make sure this won't skip any required stages
                $required = model(StageModel::class)
                    ->where('id >=', $current->id)
                    ->where('id <', $target->id)
                    ->where('workflow_id', $this->attributes['id'])
                    ->where('required', 1)
                    ->first();

                if ($required !== null) {
                    throw WorkflowsException::forSkipRequiredStage($required->name);
                }
            }

            $method = 'up';
        } else {
            $method = 'down';
        }

        // Step until we reach the target Stage
        while ($job->getStage()->id !== $target->id) {
            $this->step($job, $method);
        }
    }
}

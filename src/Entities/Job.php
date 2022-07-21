<?php

namespace Tatter\Workflows\Entities;

use RuntimeException;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;

class Job extends BaseEntity
{
    use JobFlagTrait;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts = [
        'workflow_id' => 'int',
        'stage_id'    => '?int',
    ];

    /**
     * Stored entity for the Workflow.
     *
     * @var Workflow
     */
    protected ?Workflow $workflow = null;

    /**
     * Returns the URL to show this Job.
     */
    public function getUrl(): string
    {
        $base = rtrim(config('Workflows')->routeBase, '/ ') . '/';

        return site_url($base . 'show/' . $this->attributes['id']);
    }

    /**
     * Gets the current Stage from the Workflow node tree; returns null for a completed Job.
     */
    public function getStage(): ?Stage
    {
        $this->ensureCreated();

        if ($this->stage_id === null) {
            return null;
        }

        return $this->getWorkflow()->getStageById($this->stage_id);
    }

    /**
     * Gets the Workflow.
     *
     * @throws RuntimeException
     */
    public function getWorkflow(): Workflow
    {
        $this->ensureCreated();

        if ($this->workflow === null) {
            $this->workflow = model(WorkflowModel::class)->withDeleted()->find($this->attributes['workflow_id']);

            // This should *never* happen
            if ($this->workflow === null) {
                throw new RuntimeException('Unable to locate workflow ' . $this->attributes['workflow_id'] . ' for job ' . $this->attributes['id']);
            }
        }

        return $this->workflow;
    }

    //--------------------------------------------------------------------

    /**
     * Moves through the Workflow, skipping non-required Stages but running their Action functions.
     *
     * @param string $actionId      ID of the target Action
     * @param bool   $checkRequired Whether to check for required stages while traveling
     *
     * @throws WorkflowsException
     *
     * @return array Array of boolean results from each Action's up/down method
     */
    public function travel(string $actionId, bool $checkRequired = true): array
    {
        $this->ensureCreated();

        // Make sure the target Stage exists
        if (! $target = model(StageModel::class)
            ->where('action_id', $actionId)
            ->where('workflow_id', $this->attributes['workflow_id'])
            ->first()) {
            throw WorkflowsException::forStageNotFound();
        }

        // Get the Workflow, Stages, and current Stage
        $this->getWorkflow();
        $stages  = $this->getStages();
        $current = $this->getStage();

        // Determine direction of travel
        if ($current->id < $target->id) {
            if ($checkRequired) {
                // Make sure this won't skip any required stages
                $test = model(StageModel::class)
                    ->where('id >=', $current->id)
                    ->where('id <', $target->id)
                    ->where('workflow_id', $this->attributes['workflow_id'])
                    ->where('required', 1)
                    ->first();

                if (! empty($test)) {
                    throw WorkflowsException::forSkipRequiredStage($test->name);
                }
            }

            $method = 'up';
        } elseif ($current->id > $target->id) {
            $method = 'down';
            arsort($stages);
        }
        // Already there!
        else {
            return [];
        }

        // Travel the Workflow running the appropriate method
        $results = [];

        foreach ($stages as $stage) {
            // Check if we need to run this action
            if (($method === 'up' && $stage->id > $current->id)
                || ($method === 'down' && $stage->id <= $current->id)
            ) {
                $results[$stage->id] = $stage->action->setJob($this)->{$method}();
            }

            // If the target was reached then we're done
            if ($stage->id === $target->id) {
                break;
            }
        }

        // Update the Job
        model(JobModel::class)->update($this->attributes['id'], ['stage_id' => $target->id]);

        $this->attributes['stage_id'] = $target->id;
        $this->stage                  = null;
        $this->stageFlag              = false;

        return $results;
    }
}

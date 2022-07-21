<?php

namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity\Entity;
use RuntimeException;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;

class Job extends Entity
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
     * Stored entity for the current Stage. Can be null for completed Jobs.
     */
    protected ?Stage $stage = null;

    /**
     * Whether the Stage has been loaded.
     */
    protected bool $stageChecked = false;

    /**
     * Stored entity for the Workflow.
     *
     * @var Workflow
     */
    protected ?Workflow $workflow = null;

    /**
     * Verifies the primary key to prevent operations on
     * un-created database entries.
     *
     * @throws RuntimeException
     *
     * @return $this
     */
    protected function ensureCreated(): self
    {
        if (empty($this->attributes['id'])) {
            throw new RuntimeException('Job must be created first.');
        }

        return $this;
    }

    /**
     * Returns the URL to show this Job.
     */
    public function getUrl(): string
    {
        $base = rtrim(config('Workflows')->routeBase, '/ ') . '/';

        return site_url($base . 'show/' . $this->attributes['id']);
    }

    /**
     * Gets the current Stage, or null for a completed Job.
     *
     * @throws RuntimeException
     */
    public function getStage(): ?Stage
    {
        $this->ensureCreated();

        if ($this->stageChecked) {
            return $this->stage;
        }

        if (empty($this->attributes['stage_id'])) {
            $stage = null;
        }
        // This should *never* happen
        elseif (null === $stage = model(StageModel::class)->find($this->attributes['stage_id'])) {
            throw new RuntimeException('Unable to locate Stage ' . $this->attributes['stage_id'] . ' for Job ' . $this->attributes['id']);
        }

        $this->stage        = $stage;
        $this->stageChecked = true;

        return $this->stage;
    }

    /**
     * Gets the Workflow.
     */
    public function getWorkflow(): Workflow
    {
        if ($this->workflow === null) {
            $this->workflow = model(WorkflowModel::class)->find($this->attributes['workflow_id']);
        }

        return $this->workflow;
    }

    /**
     * Gets all Stages from the Workflow.
     *
     * @return array<Stage>
     */
    public function getStages(): array
    {
        return $this->getWorkflow()->stages;
    }

    //--------------------------------------------------------------------

    /**
     * Returns the next Stage.
     */
    public function next(): ?Stage
    {
        return $this->_next($this->getStages());
    }

    /**
     * Returns the previous Stage.
     */
    public function previous(): ?Stage
    {
        // Look through all the Stages backwards
        return $this->_next(array_reverse($this->getStages()));
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

    /**
     * Returns the next Stage from an array of Stages.
     *
     * @param array<Stage> $stages
     */
    protected function _next($stages): ?Stage
    {
        // look through the stages
        $stage = current($stages);

        do {
            // Check if this is the current stage
            if ($stage->id === $this->attributes['stage_id']) {
                // Look for the next Stage
                if (! $stage = next($stages)) {
                    return null;
                }

                return $stage;
            }
        } while ($stage = next($stages));

        return null;
    }
}

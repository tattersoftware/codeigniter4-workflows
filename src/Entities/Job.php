<?php

namespace Tatter\Workflows\Entities;

use RuntimeException;
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
}

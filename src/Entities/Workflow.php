<?php

namespace Tatter\Workflows\Entities;

use Config\Services;
use OutOfBoundsException;
use Tatter\Users\Interfaces\HasPermission;
use Tatter\Workflows\Models\ExplicitModel;
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
}

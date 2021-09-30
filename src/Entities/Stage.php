<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity\Entity;
use Tatter\Workflows\Models\ActionModel;

class Stage extends Entity
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'action_id'   => 'int',
        'workflow_id' => 'int',
        'required'    => 'bool',
    ];

    /**
     * Cached entity for the associated Action.
     *
     * @var Action
     */
    private $action;

    /**
     * Passes through name requests to the Action.
     */
    public function getName(): string
    {
        return $this->getAction()->name ?? '';
    }

    /**
     * Gets the associated Action.
     */
    public function getAction(): Action
    {
        if ($this->action === null) {
            $this->action = model(ActionModel::class)->find($this->attributes['action_id']);
        }

        return $this->action;
    }
}

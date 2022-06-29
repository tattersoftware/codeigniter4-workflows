<?php

namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity\Entity;
use Tatter\Workflows\BaseAction;
use Tatter\Workflows\Factories\ActionFactory;

class Stage extends Entity
{
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'action_id'   => 'string',
        'workflow_id' => 'int',
        'required'    => 'bool',
    ];

    /**
     * Passes through name requests to the Action.
     */
    public function getName(): string
    {
        $handler = $this->getAction();

        return $handler::getAttributes()['name'];
    }

    /**
     * Gets the associated Action.
     *
     * @return class-string<BaseAction>
     */
    public function getAction(): string
    {
        return ActionFactory::find($this->attributes['action_id']);
    }
}

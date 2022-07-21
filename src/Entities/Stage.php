<?php

namespace Tatter\Workflows\Entities;

use LogicException;
use Tatter\Workflows\BaseAction;
use Tatter\Workflows\Factories\ActionFactory;

class Stage extends BaseEntity
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
     * Whether this Stage has been built as a node in a Workflow.
     * See getPrevious() and getNext().
     */
    protected bool $isNode = false;

    /**
     * Stored entity for the previous Stage.
     */
    protected ?Stage $previous = null;

    /**
     * Stored entity for the next Stage.
     */
    protected ?Stage $next = null;

    /**
     * Gets the associated Action.
     *
     * @return class-string<BaseAction>
     */
    public function getAction(): string
    {
        return ActionFactory::find($this->attributes['action_id']);
    }

    /**
     * Passes through name requests to the Action.
     */
    public function getName(): string
    {
        $handler = $this->getAction();

        return $handler::getAttributes()['name'];
    }

    /**
     * Formulate the route for this Stage (without job ID).
     * E.g.: $route = $stage->getRoute(); return redirect()->to("$route/1");
     */
    public function getRoute(): string
    {
        return '/' . config('Workflows')->routeBase . '/' . $this->attributes['action_id'] . '/';
    }

    //--------------------------------------------------------------------

    /**
     * Sets the previous Stages and indicates this as a node.
     * Should only be called by Workflow::getStages().
     *
     * @internal
     */
    public function setPrevious(?Stage $previous): void
    {
        $this->previous = $previous;
        $this->isNode   = true;
    }

    /**
     * Sets the next Stage and indicates this as a node.
     * Should only be called by Workflow::getStages().
     *
     * @internal
     */
    public function setNext(?Stage $next): void
    {
        $this->next   = $next;
        $this->isNode = true;
    }

    /**
     * Returns the previous Stage.
     */
    public function getPrevious(): ?Stage
    {
        $this->ensureNode();

        return $this->previous;
    }

    /**
     * Returns the next Stage.
     */
    public function getNext(): ?Stage
    {
        $this->ensureNode();

        return $this->next;
    }

    /**
     * Returns the previous Stage.
     *
     * @throws LogicException
     */
    private function ensureNode(): void
    {
        $this->ensureCreated();

        if ($this->isNode === false) {
            throw new LogicException('You may only use previous and next on node Stages.');
        }
    }
}

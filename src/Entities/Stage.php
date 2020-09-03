<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;
use Tatter\Workflows\Entities\Action;
use Tatter\Workflows\Models\ActionModel;

class Stage extends Entity
{
	protected $dates = ['created_at', 'updated_at'];
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
    protected $action;

    /**
     * Passes through name requests to the Action
     *
     * @return string
     */
	public function getName(): string
	{
		return $this->getAction()->name ?? '';
	}

    /**
     * Gets the associated Action
     *
     * @return Action
     */
	public function getAction(): Action
	{
		if ($this->action === null)
		{
			$this->action = model(ActionModel::class)->find($this->attributes['action_id']);
		}

		return $this->action;
	}
}

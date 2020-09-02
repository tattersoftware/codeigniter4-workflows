<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\StageModel;

class Workflow extends Entity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	// Get this workflow's stages
	// Returns ordered stage objects with their IDs as keys
	public function getStages()
	{
		$stages = [];

		foreach (model(StageModel::class)
			->where('workflow_id', $this->id)
			->orderBy('id', 'asc')
			->findAll() as $stage)
		{
			$stages[$stage->id] = $stage;
		}

		return $stages;
	}

	/**
	 * Adds an action to this workflow
	 *
	 * @param mixed $action  The action to add to this workflow; can be an Action, ID, or uid
	 * @param bool $required Whether the subsequent stage will be required
	 *
	 * @return int|string|bool  Return from StageModel::insert()
	 */
	public function addAction($action, bool $required = false)
	{
		if (empty($this->attributes['id']))
		{
			throw new \RuntimeException('Workflow must be created before adding actions.');
		}

		// Check for a UID string and look it up
		if (is_string($action) && ! is_numeric($action))
		{
			$action = model(ActionModel::class)->where('uid', $action)->first();
		}

		// Determine the ID
		if (is_numeric($action))
		{
			$id = $action;
		}
		elseif (is_object($action))
		{
			$id = $action->id;
		}
		elseif (is_array($action))
		{
			$id = $action['id'];
		}
		else
		{
			throw new \RuntimeException('Unable to locate the ID for the target action: ' . print_r($action, true));
		}

		return model(StageModel::class)->insert([
			'action_id'   => $id,
			'workflow_id' => $this->attributes['id'],
			'required'    => $required,
		]);
	}
}

<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;

use Tatter\Workflows\Models\StageModel;

class Workflow extends Entity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

	// Get this workflow's stages
	// Returns ordered stage objects with their IDs as keys
	public function getStages()
	{
		$stages = [];

		foreach ((new StageModel())
			->where('workflow_id', $this->id)
			->orderBy('id', 'asc')
			->findAll() as $stage)
		{
			$stages[$stage->id] = $stage;
		}

		return $stages;
	}
}

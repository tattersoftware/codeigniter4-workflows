<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;

use Tatter\Workflows\Models\StageModel;

class Workflow extends Entity
{
	protected $dates = ['created_at', 'updated_at'];

	// magic getter for this workflow's stages
	// returns ordered stage objects with their IDs as keys
	public function getStages()
	{
		$stages = new StageModel();
		$result = [];
		foreach ($stages
			->where('workflow_id', $this->id)
			->orderBy('id', 'asc')
			->findAll()
		as $stage)
			$result[$stage->id] = $stage;
		
		return $result;
	}
}

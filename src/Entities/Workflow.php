<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;

use Tatter\Workflows\Models\StageModel;

class Workflow extends Entity
{
	protected $dates = ['created_at', 'updated_at'];

	// magic getter for this workflow's stages
	public function getStages()
	{
		$stages = new StageModel();
		return $stages->where('workflow_id', $this->id)->orderBy('id', 'asc')->findAll();
	}
}

<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;
use Tatter\Workflows\Models\TaskModel;

class Stage extends Entity
{
	protected $dates = ['created_at', 'updated_at'];
	
	// Pass through name requests to the representative task
	public function getName()
	{
		return $this->getTask()->name ?? '';
	}
	
	// Gets the task this stage represents
	public function getTask()
	{
		return (new TaskModel())->find($this->attributes['task_id']);
	}
}

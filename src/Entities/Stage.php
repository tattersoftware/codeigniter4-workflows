<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;
use Tatter\Workflows\Entities\Task;
use Tatter\Workflows\Models\TaskModel;

class Stage extends Entity
{
	protected $dates = ['created_at', 'updated_at'];

    /**
     * Cached entity for this stage's representative task.
     *
     * @var Task
     */
    protected $task;
	
	// Passes through name requests to the parent task
	public function getName(): string
	{
		return $this->getTask()->name ?? '';
	}
	
	// Gets this stage's parent task
	public function getTask(): Task
	{
		if ($this->task === null)
		{
			$this->task = (new TaskModel())->find($this->attributes['task_id']);
		}
		
		return $this->task;
	}
}

<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;

use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Models\WorkflowModel;

class Job extends Entity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Cached entity for this job's stage.
     *
     * @var Stage
     */
    protected $stage;
	
	// Returns the next task for this job
	public function next()
	{
		// look through all this job's stages
		$stages = $this->stages;
		return $this->nextHelper($stages);
	}
	
	// Returns the previous task for this job
	public function previous()
	{
		// look through all this job's stages
		$stages = $this->stages;
		array_reverse($stages);
		
		return $this->nextHelper($stages);
	}
	
	// Returns the next task from an array of stages
	protected function nextHelper($stages)
	{
		// look through the stages
		$stage = current($stages);
		do
		{
			// check if this is the current stage
			if ($stage->id == $this->attributes['stage_id']):
				// matched! look for the next stage
				$stage = next($stages);
				if (empty($stage)):
					return false;
				endif;
				
				// get the task from this stage
				$tasks = new TaskModel();
				$task = $tasks->find($stage->task_id);
				if (empty($task)):
					return false;
				endif;
				
				// set this as the current job and return
				$task->job = $this;
				return $task;
			endif;
		} while ($stage = next($stages));

		return false;
	}
	
	// Gets the current stage
	public function getStage(): Stage
	{
		if ($this->stage === null)
		{
			$this->stage = (new StageModel())->find($this->attributes['stage_id']);
		}
		
		return $this->stage;
	}
	
	// Gets all stages this job will go through
	public function getStages()
	{
		$workflow = (new WorkflowModel())->find($this->attributes['workflow_id']);
		return $workflow->stages;
	}
}

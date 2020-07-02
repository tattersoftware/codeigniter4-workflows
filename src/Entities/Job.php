<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;

use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\ActionModel;
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
	
	// Returns the next action for this job
	public function next()
	{
		// look through all this job's stages
		$stages = $this->stages;
		return $this->nextHelper($stages);
	}
	
	// Returns the previous action for this job
	public function previous()
	{
		// look through all this job's stages
		$stages = $this->stages;
		array_reverse($stages);
		
		return $this->nextHelper($stages);
	}
	
	// Returns the next action from an array of stages
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
				
				// get the action from this stage
				$actions = new ActionModel();
				$action = $actions->find($stage->action_id);
				if (empty($action)):
					return false;
				endif;
				
				// set this as the current job and return
				$action->job = $this;
				return $action;
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

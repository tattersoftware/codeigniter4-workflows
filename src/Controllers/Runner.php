<?php namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Config\Services;
use CodeIgniter\Exceptions\PageNotFoundException;

use Tatter\Workflows\Entities\Task;
use Tatter\Workflows\Exceptions\WorkflowsException;

use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Models\WorkflowModel;

class Runner extends Controller
{
	public function __construct()
	{
		$this->jobs       = new JobModel();
		$this->stages     = new StageModel();
		$this->tasks      = new TaskModel();
		$this->workflows  = new WorkflowModel();
		
		$this->config = class_exists('\Config\Workflows') ?
			new \Config\Workflows() : new \Tatter\Workflows\Config\Workflows();
	}
	
	// start a new job in the given workflow
	public function new($workflowId = null)
	{
		// get the workflow, or if not provided then use the first
		$workflow = ($workflowId) ?
			$this->workflows->find($workflowId) : $this->workflows->first();
		if (empty($workflow))
			throw WorkflowsException::forWorkflowNotFound();
		
		// determine starting point
		$stages = $workflow->stages;
		if (empty($stages))
			throw WorkflowsException::forMissingStages();
		$stage = reset($stages);
		
		// come up with an initial name
		helper('text');
		$name = random_string('alnum', 4);
		
		// create the job
		$row = [
			'name'        => $name,
			'workflow_id' => $workflowId,
			'stage_id'    => $stage->id,
		];
		$jobId = $this->jobs->insert($row, true);
		
		// send to the first task
		$task = $this->tasks->find($stage->task_id);
		$route = "/{$this->config->routeBase}/{$task->uid}/{$jobId}";
		return redirect()->to($route)->with('success', lang('Workflows.newJobSuccess'));
	}
	
	// receives route input and handles task coordination
	public function run(...$params)
	{
		if (empty($params))
			throw PageNotFoundException::forPageNotFound();
		
		// determine task by the route
		$route = array_shift($params);
		$task = $this->tasks->where('uid', $route)->first();
		if (empty($task))
			throw WorkflowsException::forTaskNotFound();
		
		// get the job ID
		$jobId = array_shift($params);
		if (empty($jobId))
			throw WorkflowsException::forMissingJobId($route);
		
		// load the job
		$job = $this->jobs->find($jobId);
		if (empty($job))
			throw WorkflowsException::forJobNotFound();

		// load the workflow
		$workflow = $this->workflows->find($job->workflow_id);
		
		// get the current stage
		$stage = $this->stages->find($job->stage_id);
		if (empty($stage))
			throw WorkflowsException::forStageNotFound();
		
		// check if the requested task is different from the job's current task
		if ($stage->task_id != $task->id):
			// locate the requested task's stage in the workflow
			$stages = $workflow->stages;
			foreach ($stages as $thisStage):
				if ($thisStage->task_id == $task->id):
					$newStage = $thisStage;
					break;
				endif;
			endforeach;
			
			// verify
			if (empty($newstage)):
				throw new \RuntimeException('Invalid task requested');
			endif;
			
			// determine workflow direction
			$asc = ($thisStage->id > $stage->id);
		endif;
		
		// determine request method
		$request = Services::request();
		$method = $request->getMethod();
		
		// make sure this task supports the requested method
		$instance = new $task->class();
		if (! is_callable([$instance, $method]))
			throw WorkflowsException::forUnsupportedTaskMethod($task->name, $method);
		
		// set the job reference and run the task method
		$instance->job = $job;
		$result = $instance->{$method}();
		
	}
}

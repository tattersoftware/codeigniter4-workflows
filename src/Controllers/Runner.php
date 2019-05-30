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
	protected $job;
	protected $stage;
	protected $task;
	protected $workflow;	
	
	public function __construct()
	{
		// preload the models
		$this->jobs       = new JobModel();
		$this->stages     = new StageModel();
		$this->tasks      = new TaskModel();
		$this->workflows  = new WorkflowModel();
		
		// preload the config class
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
		
		// parse route parameters
		$this->parseRoute($params);
		
		// if the requested task differs from the job's current task then travel the workflow
		if ($this->task->id != $this->stage->task_id)
			$this->travel();
		
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
	
	// validate and parse values from a route
	protected function parseRoute($params)
	{
		// strip off task & job identifiers
		$route = array_shift($params);
		$jobId = array_shift($params);
		if (empty($jobId))
			throw WorkflowsException::forMissingJobId($route);
		
		// lookup the task by its route
		$this->task = $this->tasks->where('uid', $route)->first();
		if (empty($this->task))
			throw WorkflowsException::forTaskNotFound();

		// load the job and its workflow and stage
		$this->job = $this->jobs->find($jobId);
		if (empty($this->job))
			throw WorkflowsException::forJobNotFound();

		$this->workflow = $this->workflows->find($this->job->workflow_id);
		if (empty($this->workflow))
			throw WorkflowsException::forWorkflowNotFound();

		$this->stage = $this->stages->find($this->job->stage_id);
		if (empty($this->stage))
			throw WorkflowsException::forStageNotFound();
	
		return true;
	}
	
	// move a job through the workflow, skipping non-required stages but running their task functions
	protected function travel()
	{
		$current = $this->stage;
		
		// get the desired stage from the workflow
		$target = $this->stages
			->where('task_id', $this->task->id)
			->where('workflow_id', $this->workflow->id)
			->first();
		if (empty($target))
			throw WorkflowsException::forStageNotFound();

		// get all this workflow's stages
		$stages = $this->workflow->stages;
		
		// determine direction of travel
		if ($this->stage->id < $target):			
			// make sure this won't skip any required stages
			$test = $this->stages
				->where('id >=', $current->id)
				->where('id <', $target->id)
				->where('workflow_id', $this->workflow->id)
				->where('required', 1)
				->first();
			if (! empty($test)):
				$name = $this->tasks->find($stage->task_id)->name;
				throw WorkflowsException::forSkipRequiredStage($name);
			endif;
			
			$method = 'up';
		else:
			$method = 'down';
			arsort($stages);
		endif;
		
		// travel the workflow running the appropriate method
		$results = [];
		foreach ($stages as $stage):
			// check if we need to run this task
			if (
				($method == 'up' && $stage->id >= $current->id) ||
				($method == 'down' && $stage->id <= $current->id)
			):
				$task = $this->tasks->find($stage->task_id);
				$results[$stage->id] = $this->callTaskMethod($task, $method);
			endif;
			
			// if the target was reached then go no farther
			if ($stage->id == $target->id):
				break;
			endif;
		endforeach;
		
		// update the job
		$this->jobs->update($this->job->id, ['stage_id' => $target->id]);
		
		return $results;
	}
	
	// validate and run the specified method for a task
	protected function callTaskMethod($task, $method)
		// make sure this task supports the requested method
		$instance = new $task->class();
		if (! is_callable([$instance, $method]))
			throw WorkflowsException::forUnsupportedTaskMethod($task->name, $method);
		
		// set the job reference and run the task method
		$instance->job = $job;
		return $instance->{$method}();
	}
}

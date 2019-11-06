<?php namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Config\Services;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

use Tatter\Workflows\Entities\Task;
use Tatter\Workflows\Exceptions\WorkflowsException;

use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Models\WorkflowModel;


// SHOULD IMPLEMENT websafe ROUTES https://codeigniter4.github.io/CodeIgniter4/incoming/routing.html#resource-routes
class Runner extends Controller
{
	protected $job;
	protected $stage;
	protected $task;
	protected $workflow;	
	
	public function __construct()
	{		
		// preload the config class
		$this->config = config('Workflows');
			
		// preload the models
		$this->jobs       = new $this->config->jobModel();
		$this->stages     = new StageModel();
		$this->tasks      = new TaskModel();
		$this->workflows  = new WorkflowModel();
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
		$name = 'My New Job';
		
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
		
		// look for a resume request (job ID, nothing else)
		if (count($params) == 1 && is_numeric($params[0]))
			return $this->resume($params[0]);
		
		// parse route parameters
		$result = $this->parseRoute($params);
		if (! empty($result))
			return $result;

		// intercept completed jobs
		if (empty($this->stage))
			return redirect()->to('/');
		
		// if the requested task differs from the job's current task then travel the workflow
		if ($this->task->id != $this->stage->task_id)
			$this->travel();
		
		// determine request method & run corresponding method on the task
		$request = Services::request();
		$method = $request->getMethod();
		$result = $this->callTaskMethod($this->task, $method);
		
		// string: display
		if (is_string($result)):
			return $result;
		
		// true: task complete, move on
		elseif ($result === true):
		
			// get the next stage
			$stage = $this->stages
				->where('workflow_id', $this->workflow->id)
				->where('id >', $this->stage->id)
				->orderBy('id', 'asc')
				->first();

			// if no more stages then wrap up
			if (empty($stage)):
				return $this->complete();
			endif;
			
			// update the job
			$this->jobs->update($this->job->id, ['stage_id' => $stage->id]);
			
			// get the next task and redirect
			$task = $this->tasks->find($stage->task_id);
			$route = "/{$this->config->routeBase}/{$task->uid}/{$this->job->id}";
			return redirect()->to($route);
			
		// array: treat as error messages
		elseif (is_array($result)):
			if ($this->config->silent):
				return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'errors' => $result]);
			else:
				throw new \RuntimeException(implode('. ', $result));
			endif;

		elseif ($result instanceof RedirectResponse):
			return $result;
			
		// borked
		else:
			if ($this->config->silent):
				return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'error' => lang('Workflows.invalidTaskReturn')]);
			else:
				throw new \RuntimeException(lang('Workflows.invalidTaskReturn'));
			endif;
		endif;
	}
	
	// delete a job
	public function delete($jobId)
	{		
		// grab the job
		$this->job = $this->jobs->find($jobId);
		
		// (soft) delete the job
		$this->jobs->delete($jobId);
		return view($this->config->views['deleted'], ['layout' => $this->config->layouts['public'], 'job' => $this->job]);
	}
	
	// validate and parse values from a route
	protected function parseRoute($params)
	{
		// strip off task & job identifiers
		$route = array_shift($params);
		$jobId = array_shift($params);
		if (empty($jobId)):
			if ($this->config->silent):
				return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'error' => lang('Workflows.routeMissingJobId', [$route])]);
			else:
				throw WorkflowsException::forMissingJobId($route);
			endif;
		endif;

		// lookup the task by its route
		$this->task = $this->tasks->where('uid', $route)->first();
		if (empty($this->task))
			throw WorkflowsException::forTaskNotFound();

		// load the job and its workflow and stage
		$this->job = $this->jobs->find($jobId);
		if (empty($this->job)):
			if ($this->config->silent):
				return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'error' => lang('Workflows.jobNotFound')]);
			else:
				throw WorkflowsException::forJobNotFound();
			endif;
		endif;			

		$this->workflow = $this->workflows->find($this->job->workflow_id);
		if (empty($this->workflow)):
			if ($this->config->silent):
				return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'error' => lang('Workflows.workflowNotFound')]);
			else:
				throw WorkflowsException::forWorkflowNotFound();
			endif;
		endif;
		
		// stage can be empty (e.g. completed job)
		if ($this->job->stage_id)
			$this->stage = $this->stages->find($this->job->stage_id);
	}

	// pick a job back up at its current stage
	protected function resume($jobId)
	{
		// load the job, stage, and task
		$this->job = $this->jobs->find($jobId);
		if (empty($this->job)):
			if ($this->config->silent):
				return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'error' => lang('Workflows.jobNotFound')]);
			else:
				throw WorkflowsException::forJobNotFound();
			endif;
		endif;
		
		if (empty($this->job->stage_id))
			return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'message' => lang('Workflows.jobAlreadyComplete')]);
		$this->stage = $this->stages->find($this->job->stage_id);
		if (empty($this->stage))
			return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'error' => lang('Workflows.jobAlreadyComplete')]);
	
		$task = $this->tasks->find($this->stage->task_id);
		$route = "/{$this->config->routeBase}/{$task->uid}/{$this->job->id}";
		return redirect()->to($route);
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
				$name = $this->tasks->find($test->task_id)->name;
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
	{
		// make sure this task supports the requested method
		$instance = new $task->class();
		if (! is_callable([$instance, $method]))
			throw WorkflowsException::forUnsupportedTaskMethod($task->name, $method);
		
		// set the references and run the task method
		$instance->job        = $this->job;
		$instance->jobs       = $this->jobs;
		$instance->config     = $this->config;
		$instance->renderer   = Services::renderer();
		$instance->request    = $this->request;
		$instance->validation = Services::validation();

		return $instance->{$method}();
	}
	
	// complete the current job
	protected function complete()
	{
		// update the job
		$this->jobs->update($this->job->id, ['stage_id' => null]);
		return view($this->config->views['complete'], ['layout' => $this->config->layouts['public'], 'job' => $this->job]);
	}
}

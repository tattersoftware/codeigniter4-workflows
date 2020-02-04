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


/**
 * Class Runner
 *
 * Functions as a super-controller, routing jobs to their specific tasks
 * and task functions with included metadata.
 *
 */
class Runner extends Controller
{
	protected $job;
	protected $stage;
	protected $task;
	protected $workflow;	
	
	public function __construct()
	{		
		// Preload the config class
		$this->config = config('Workflows');
			
		// Preload the models
		$this->jobs      = new $this->config->jobModel();
		$this->stages    = new StageModel();
		$this->tasks     = new TaskModel();
		$this->workflows = new WorkflowModel();
	}
	
    /**
     * Display a job.
     *
     * @param string $jobId  ID of the job (int)
     *
     * @return string
     */
	public function show(string $jobId = null)
	{
		// Load the job
		if (! $job = $this->jobs->find($jobId))
		{
			if ($this->config->silent)
			{
				return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'error' => lang('Workflows.jobNotFound')]);
			}
			else
			{
				throw WorkflowsException::forJobNotFound();
			}
		}

		return view($this->config->views['job'], ['layout' => $this->config->layouts['public'], 'job' => $job]);
	}

    /**
     * Start a new job in the given workflow.
     *
     * @param string $workflowId  ID of the workflow to use for the new job (int)
     *
     * @return RedirectResponse
     */
	public function new(string $workflowId = null): RedirectResponse
	{
		// Get the workflow, or if not provided then use the first available
		$workflow = ($workflowId) ? $this->workflows->find($workflowId) : $this->workflows->first();
		if (empty($workflow))
		{
			throw WorkflowsException::forWorkflowNotFound();
		}

		// Determine the starting point
		$stages = $workflow->stages;
		if (empty($stages))
		{
			throw WorkflowsException::forMissingStages();
		}
		$stage = reset($stages);
		
		// Create the job
		$row = [
			'name'        => 'My New Job',
			'workflow_id' => $workflow->id,
			'stage_id'    => $stage->id,
		];
		$jobId = $this->jobs->insert($row, true);
		
		// Send to the first task
		$task  = $stage->task;
		$route = "/{$this->config->routeBase}/{$task->uid}/{$jobId}";

		return redirect()->to($route)->with('success', lang('Workflows.newJobSuccess'));
	}
	
    /**
     * Receives route input and handles task coordination.
     *
     * @param mixed $params  Parameters coming from the router (so all strings)
     *
     * @return string|RedirectResponse  A view to display or a RedirectResponse
     */
	public function run(...$params)
	{
		if (empty($params))
		{
			throw PageNotFoundException::forPageNotFound();
		}
		
		// Look for a resume request (job ID, nothing else)
		if (count($params) == 1 && is_numeric($params[0]))
		{
			return $this->resume($params[0]);
		}

		// Parse the route parameters
		$result = $this->parseRoute($params);
		
		// If parseRoute() returned an error view then display it
		if (! empty($result))
		{
			return $result;
		}

		// Intercept jobs that are already completed
		if (empty($this->stage))
		{
			return redirect()->to(site_url($this->config->routeBase . '/show/' . $this->job->id));
		}
		
		// If the requested task differs from the job's current task then travel the workflow
		if ($this->task->id != $this->stage->task_id)
		{
			$this->travel();
		}
		
		// Check the task's role against a potential current user
		if (! $this->checkRole())
		{
			return view($this->config->views['filter'], ['layout' => $this->config->layouts['public'], 'job' => $this->job]);
		}
		
		// Determine the request method & run the corresponding method on the task
		$result = $this->callTaskMethod($this->task, $this->request->getMethod());
		
		// Handle return values by their type:
		// string: display
		if (is_string($result))
		{
			return $result;
		}

		// true: task complete, move on
		elseif ($result === true)
		{		
			// Get the next stage
			$stage = $this->stages
				->where('workflow_id', $this->workflow->id)
				->where('id >', $this->stage->id)
				->orderBy('id', 'asc')
				->first();

			// If no more stages then wrap up
			if (empty($stage))
			{
				return $this->complete();
			}
			
			// Update the job
			$this->jobs->update($this->job->id, ['stage_id' => $stage->id]);
			
			// Get the next task and redirect
			$task  = $stage->task;
			$route = "/{$this->config->routeBase}/{$task->uid}/{$this->job->id}";
			return redirect()->to($route);
		}

		// array: treat as error messages
		elseif (is_array($result))
		{
			if ($this->config->silent)
			{
				return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'errors' => $result]);
			}
			else
			{
				throw new \RuntimeException(implode('. ', $result));
			}
		}
		
		// RedirectResponse: redirect
		elseif ($result instanceof RedirectResponse)
		{
			return $result;
		}
			
		// borked
		if ($this->config->silent)
		{
			return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'error' => lang('Workflows.invalidTaskReturn')]);
		}
		else
		{
			throw new \RuntimeException(lang('Workflows.invalidTaskReturn'));
		}
	}
	
    /**
     * Deletes a job.
     *
     * @param string $jobId  ID of the job to remove (int)
     *
     * @return string  A view notifying the user that the job was removed.
     */
	public function delete(string $jobId)
	{		
		// Verify the job
		$this->job = $this->jobs->find($jobId);
		
		// Delete the job (soft)
		$this->jobs->delete($jobId);

		return view($this->config->views['deleted'], ['layout' => $this->config->layouts['public'], 'job' => $this->job]);
	}

    /**
     * Validates and parses values from a route.
     *
     * @param mixed $params  Parameters coming from the router (so all strings)
     *
     * @return string|null  Optional view to display instead of continuing
     */
	protected function parseRoute($params): ?string
	{
		// Strip off the task & job identifiers
		$route = array_shift($params);
		$jobId = array_shift($params);
		
		if (empty($jobId))
		{
			if ($this->config->silent)
			{
				return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'error' => lang('Workflows.routeMissingJobId', [$route])]);
			}
			else
			{
				throw WorkflowsException::forMissingJobId($route);
			}
		}

		// Look up the task by its route
		$this->task = $this->tasks->where('uid', $route)->first();
		
		if (empty($this->task))
		{
			throw WorkflowsException::forTaskNotFound();
		}

		// Load the job
		$this->job = $this->jobs->find($jobId);
		
		if (empty($this->job))
		{
			if ($this->config->silent)
			{
				return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'error' => lang('Workflows.jobNotFound')]);
			}
			else
			{
				throw WorkflowsException::forJobNotFound();
			}
		}

		// Verify the workflow
		$this->workflow = $this->workflows->find($this->job->workflow_id);
		
		if (empty($this->workflow))
		{
			if ($this->config->silent)
			{
				return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'error' => lang('Workflows.workflowNotFound')]);
			}
			else
			{
				throw WorkflowsException::forWorkflowNotFound();
			}
		}
		
		// The stage can be empty (completed job)
		if ($this->job->stage_id)
		{
			$this->stage = $this->stages->find($this->job->stage_id);
		}

		return null;
	}

    /**
     * Resume a job at its current stage.
     *
     * @param string $jobId  ID of the job to resume (int)
     *
     * @return string|RedirectResponse  A view to display or a RedirectResponse
     */
	protected function resume($jobId)
	{
		// Load the job, stage, and task
		$this->job = $this->jobs->find($jobId);

		if (empty($this->job))
		{
			if ($this->config->silent)
			{
				return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'error' => lang('Workflows.jobNotFound')]);
			}
			else
			{
				throw WorkflowsException::forJobNotFound();
			}
		}

		// If the job is completed then display a message and quit
		if (empty($this->job->stage_id))
		{
			return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'message' => lang('Workflows.jobAlreadyComplete')]);
		}

		$this->stage = $this->stages->find($this->job->stage_id);
		if (empty($this->stage))
		{
			return view($this->config->views['messages'], ['layout' => $this->config->layouts['public'], 'error' => lang('Workflows.jobAlreadyComplete')]);
		}

		$task = $this->tasks->find($this->stage->task_id);
		$route = "/{$this->config->routeBase}/{$task->uid}/{$this->job->id}";

		return redirect()->to($route);
	}

    /**
     * Move the current job through the workflow, skipping non-required stages but running their task functions.
     *
     * @return array  Array of boolean results from each task's up/down method
     */
	protected function travel()
	{
		$current = $this->stage;
		
		// Get the desired stage from the workflow
		$target = $this->stages
			->where('task_id', $this->task->id)
			->where('workflow_id', $this->workflow->id)
			->first();

		if (empty($target))
		{
			throw WorkflowsException::forStageNotFound();
		}

		// Get all this workflow's stages
		$stages = $this->workflow->stages;
		
		// Determine direction of travel
		if ($this->stage->id < $target)
		{
			// Make sure this won't skip any required stages
			$test = $this->stages
				->where('id >=', $current->id)
				->where('id <', $target->id)
				->where('workflow_id', $this->workflow->id)
				->where('required', 1)
				->first();

			if (! empty($test))
			{
				$name = $this->tasks->find($test->task_id)->name;
				throw WorkflowsException::forSkipRequiredStage($name);
			}
			
			$method = 'up';
		}
		else
		{
			$method = 'down';
			arsort($stages);
		}

		// Travel the workflow running the appropriate method
		$results = [];
		foreach ($stages as $stage)
		{
			// Check if we need to run this task
			if (
				($method == 'up'   && $stage->id >= $current->id) ||
				($method == 'down' && $stage->id <= $current->id)
			)
			{
				$task = $this->tasks->find($stage->task_id);
				$results[$stage->id] = $this->callTaskMethod($task, $method);
			}
			
			// If the target was reached then we're done
			if ($stage->id == $target->id)
			{
				break;
			}
		}
		
		// Update the job
		$this->jobs->update($this->job->id, ['stage_id' => $target->id]);
		
		return $results;
	}

    /**
     * Checks if role filter is enabled and if the current user may run this task.
     *
     * @return bool  True if the task may continue
     */
	protected function checkRole(): bool
	{
		// If role filtering isn't set up then allow through
		if (! function_exists('has_permission'))
		{
			return true;
		}

		// Anyone can run user tasks
		if (empty($this->task->role) || $this->task->role == 'user')
		{
			return true;
		}

		// Otherwise check for task role permission
		return has_permission($this->task->role);
	}

    /**
     * Validate and run the specified method for a task.
     *
     * @param Task   $task    Entity for the task to run
     * @param string $method  Name of the method to call
     *
     * @return mixed  Result of the task method
     */
	protected function callTaskMethod(Task $task, string $method)
	{
		// make sure this task supports the requested method
		$instance = new $task->class();
		if (! is_callable([$instance, $method]))
		{
			throw WorkflowsException::forUnsupportedTaskMethod($task->name, $method);
		}

		// Set the references and run the task method
		$instance->job        = $this->job;
		$instance->jobs       = $this->jobs;
		$instance->config     = $this->config;
		$instance->renderer   = Services::renderer();
		$instance->request    = $this->request;
		$instance->validation = Services::validation();

		return $instance->{$method}();
	}

    /**
     * Complete the current job.
     *
     * @return string  View of the completion method
     */
	protected function complete()
	{
		// Update the job
		$this->jobs->update($this->job->id, ['stage_id' => null]);

		return view($this->config->views['complete'], ['layout' => $this->config->layouts['public'], 'job' => $this->job]);
	}
}

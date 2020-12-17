<?php namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Workflows\Config\Workflows as WorkflowsConfig;
use Tatter\Workflows\Entities\Action;
use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\JoblogModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\WorkflowModel;

/**
 * Class Runner
 *
 * Functions as a super-controller, routing jobs to their specific actions
 * and action functions with included metadata.
 */
class Runner extends Controller
{
	/**
	 * @var WorkflowsConfig
	 */
	protected $config;

	/**
	 * @var JobModel  Module version or extension thereof
	 */
	protected $jobs;

	/**
	 * @var Job|null
	 */
	protected $job;

	/**
	 * @var Stage|null
	 */
	protected $stage;

	/**
	 * @var Action|null
	 */
	protected $action;

	/**
	 * @var Stage|null
	 */
	protected $workflow;

	/**
	 * Preload the config class and Model for jobs.
	 */
	public function __construct()
	{
		$this->config = config('Workflows');
		$this->jobs   = model($this->config->jobModel);
	}

	/**
	 * Display a job.
	 *
	 * @param string $jobId ID of the job (int)
	 *
	 * @return string
	 * @throws WorkflowsException
	 */
	public function show(string $jobId = null): string
	{
		// Load the job
		if (! $job = $this->jobs->find($jobId))
		{
			if ($this->config->silent)
			{
				return view($this->config->views['messages'], [
					'layout' => $this->config->layouts['public'],
					'error'  => lang('Workflows.jobNotFound'),
				]);
			}

			throw WorkflowsException::forJobNotFound();
		}

		return view($this->config->views['job'], [
			'job'    => $job,
			'logs'   => model(JoblogModel::class)->findWithStages($job->id),
			'layout' => $this->config->layouts['public'],
		]);
	}

	/**
	 * Resume a Job at its current Stage.
	 *
	 * @param string $jobId ID of the job to resume (int)
	 *
	 * @return string|RedirectResponse  A view to display or a RedirectResponse
	 */
	protected function resume($jobId)
	{
		// Get the Job
		if ($jobId)
		{
			/** @var Job $job */
			$job = $this->jobs->find($jobId);
		}

		if (empty($job))
		{
			if ($this->config->silent)
			{
				return view($this->config->views['messages'], [
					'layout' => $this->config->layouts['public'],
					'error'  => lang('Workflows.jobNotFound'),
				]);
			}

			throw WorkflowsException::forJobNotFound();
		}

		// If the Job is completed then display a message and quit
		if (empty($job->stage_id))
		{
			return view($this->config->views['messages'], [
				'layout'  => $this->config->layouts['public'],
				'message' => lang('Workflows.jobAlreadyComplete'),
			]);
		}

		// Check for a current Stage
		if (! $stage = model(StageModel::class)->find($job->stage_id))
		{
			return view($this->config->views['messages'], [
				'layout' => $this->config->layouts['public'],
				'error'  => lang('Workflows.jobAlreadyComplete'),
			]);
		}

		return redirect()->to($stage->action->getRoute($job->id));
	}

	/**
	 * Start a new Job in the given Workflow.
	 *
	 * @param string|null $workflowId ID of the Workflow to use for the new Job (int)
	 *
	 * @return RedirectResponse
	 * @throws WorkflowsException
	 */
	public function new(string $workflowId = null): RedirectResponse
	{
		// Get the workflow, or if not provided then use the first available
		$workflow = ($workflowId)
			? model(WorkflowModel::class)->find($workflowId)
			: model(WorkflowModel::class)->first();

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
		$jobId = $this->jobs->insert([
			'name'        => 'My New Job',
			'workflow_id' => $workflow->id,
			'stage_id'    => $stage->id,
		]);

		// Send to the first action
		$action = $stage->action;
		$route  = "/{$this->config->routeBase}/{$action->uid}/{$jobId}";

		return redirect()->to($route)->with('success', lang('Workflows.newJobSuccess'));
	}

	/**
	 * Deletes a job.
	 *
	 * @param string $jobId ID of the job to remove (int)
	 *
	 * @return string  A view notifying the user that the job was removed.
	 * @throws PageNotFoundException
	 */
	public function delete(string $jobId): string
	{
		// Verify the job
		if (! $job = $this->jobs->find($jobId))
		{
			throw PageNotFoundException::forPageNotFound();
		}

		// Delete the job (soft)
		$this->jobs->delete($jobId);

		return view($this->config->views['deleted'], [
			'layout' => $this->config->layouts['public'],
			'job'    => $job,
		]);
	}

	/**
	 * Receives route input and handles action coordination.
	 *
	 * @param string ...$params Parameters coming from the router (so all strings)
	 *
	 * @return string|ResponseInterface  A view to display or a Response
	 * @throws PageNotFoundException
	 */
	public function run(string ...$params)
	{
		if (empty($params))
		{
			throw PageNotFoundException::forPageNotFound();
		}

		// Look for a resume request (job ID, nothing else)
		if (count($params) === 1 && is_numeric($params[0]))
		{
			return $this->resume($params[0]);
		}

		// Parse the route parameters
		$parsed = $this->parseRoute($params);

		// If parseRoute() returned an error view then display it
		if (is_string($parsed))
		{
			return $parsed;
		}

		// Extract parsed variables
		list($action, $job, $stage) = $parsed;

		// Intercept Jobs that are already completed
		if (empty($stage))
		{
			return redirect()->to(site_url($this->config->routeBase . '/show/' . $job->id));
		}

		// If the requested Action differs from the job's current action then travel the workflow
		if ($action->id !== $stage->action_id)
		{
			$job->travel($action->id);
		}

		// Check the Action's role against a potential current user
		if (! $action->mayAccess())
		{
			return view($this->config->views['filter'], [
				'layout' => $this->config->layouts['public'],
				'job'    => $job,
			]);
		}

		// Determine the request method and run the corresponding Action method
		$method = $this->request->getMethod();
		$result = $action->setJob($job)->$method();

		// Handle return values by their type
		return $this->parseResult($result, $job);
	}

	/**
	 * Validates and parses values from a route.
	 *
	 * @param array $params Parameters coming from the router (so all strings)
	 *
	 * @return array|string  Array of parsed data, or a string view to display instead of continuing
	 * @throws WorkflowsException
	 */
	protected function parseRoute(array $params)
	{
		// Strip off the Action & Job identifiers
		$uid   = array_shift($params);
		$jobId = array_shift($params);

		// Verify the ID
		if (empty($jobId) || ! is_numeric($jobId))
		{
			if ($this->config->silent)
			{
				return view($this->config->views['messages'], [
					'layout' => $this->config->layouts['public'],
					'error'  => lang('Workflows.routeMissingJobId', [$uid]),
				]);
			}

			throw WorkflowsException::forMissingJobId($uid);
		}

		// Look up the Action by its UID
		if (! $action = model(ActionModel::class)->where('uid', $uid)->first())
		{
			throw WorkflowsException::forActionNotFound();
		}

		// Load the Job
		if (! $job = $this->jobs->find($jobId))
		{
			if ($this->config->silent)
			{
				return view($this->config->views['messages'], [
					'layout' => $this->config->layouts['public'],
					'error'  => lang('Workflows.jobNotFound'),
				]);
			}

			throw WorkflowsException::forJobNotFound();
		}

		// Verify the Workflow
		if (! $workflow = model(WorkflowModel::class)->find($job->workflow_id))
		{
			if ($this->config->silent)
			{
				return view($this->config->views['messages'], [
					'layout' => $this->config->layouts['public'],
					'error'  => lang('Workflows.workflowNotFound'),
				]);
			}

			throw WorkflowsException::forWorkflowNotFound();
		}

		// stage_id may be empty (completed Job)
		$stage = $job->stage_id ? model(StageModel::class)->find($job->stage_id) : null;

		return [
			$action,
			$job,
			$stage,
		];
	}

	/**
	 * Parses the result of an Action method call.
	 *
	 * @param mixed $result Result from the Action method
	 * @param Job   $job    The current Job
	 *
	 * @return string|ResponseInterface  A view to display or a Response
	 * @throws WorkflowsException
	 */
	protected function parseResult($result, Job $job)
	{
		// Simple string for display (usually a view)
		if (is_string($result))
		{
			return $result;
		}

		// Simple Response
		if ($result instanceof ResponseInterface)
		{
			return $result;
		}

		// Boolean true means this Stage is complete
		if ($result === true)
		{
			// Get the next Stage
			if ($stage = $job->next())
			{
				// Travel to the target Action
				$job->travel($stage->action_id);

				// Redirect to the next Action
				return redirect()->to($stage->action->getRoute($job->id));
			}

			// Update the Job as complete
			$this->jobs->update($job->id, ['stage_id' => null]);

			return view($this->config->views['complete'], [
				'layout' => $this->config->layouts['public'],
				'job'    => $job,
			]);
		}

		// Arrays are error messages
		if (is_array($result))
		{
			if ($this->config->silent)
			{
				return view($this->config->views['messages'], [
					'layout' => $this->config->layouts['public'],
					'errors' => $result,
				]);
			}

			throw new WorkflowsException(implode('. ', $result));
		}

		// Borked
		if ($this->config->silent)
		{
			return view($this->config->views['messages'], [
				'layout' => $this->config->layouts['public'],
				'error'  => lang('Workflows.invalidActionReturn'),
			]);
		}

		throw new WorkflowsException(lang('Workflows.invalidActionReturn'));
	}
}

<?php namespace Tatter\Workflows;

use CodeIgniter\HTTP\RequestInterface;
use Tatter\Handlers\BaseHandler;
use Tatter\Workflows\Config\Workflows as WorkflowsConfig;
use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\JobModel;
use RuntimeException;

/**
 * Class reference and common functions for all Actions.
 */
abstract class BaseAction extends BaseHandler
{
	/**
	 * Attributes to Tatter\Handlers, implemented by child class
	 *
	 * @var array<string>|null
	 */
	protected $attributes;

	/**
	 * Default set of attributes and their types.
	 *
	 * @var array<string>|null
	 */
	protected $defaults = [
		'category' => '',
		'name'     => '',
		'uid'      => '',
		'role'     => 'user',
		'icon'     => 'fas fa-tasks',
		'summary'  => '',
	];

	/**
	 * @var WorkflowsConfig
	 */
	public $config;

	/**
	 * @var Job|null
	 */
	public $job;

	/**
	 * @var JobModel
	 */
	public $jobs;

	/**
	 * @var RequestInterface
	 */
	public $request;

	/**
	 * Sets up common resources for Actions.
	 *
	 * @param WorkflowsConfig|null $config
	 * @param Job|null $job
	 * @param JobModel|null $jobs
	 * @param RequestInterface|null $request
	 */
	public function __construct(WorkflowsConfig $config = null, Job $job = null, JobModel $jobs = null, RequestInterface $request = null)
	{
		parent::__construct();

		$this->config  = $config ?? config('Workflows');
		$this->job     = $job;
		$this->jobs    = $jobs ?? model($this->config->jobModel);
		$this->request = $request ?? service('request');
	}

	//--------------------------------------------------------------------

	/**
	 * Creates the database record for this class based on its definition.
	 *
	 * @return int The ID of the new/exsiting class
	 *
	 * @throws RuntimeException for insert failures
	 */
	public function register(): int
	{
		$actions = model(ActionModel::class);

		// Check for an existing entry
		if ($action = $actions->where('uid', $this->attributes['uid'])->first())
		{
			return $action->id;
		}

		$row          = $this->toArray();
		$row['class'] = get_class($this);

		return (int) $actions->insert($row);
	}

	/**
	 * Deletes this action from the database (soft).
	 *
	 * @return bool Result from the model
	 */
	public function remove(): bool
	{
		return model(ActionModel::class)->where('uid', $this->attributes['uid'])->delete();
	}

	/**
	 * Sets the Job for this Action to run against.
	 *
	 * @param Job $job
	 *
	 * @return $this
	 */
	public function setJob(Job $job): self
	{
		$this->job = $job;

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Runs when a job progresses forward through the workflow.
	 *
	 * @return mixed
	 */
	public function up()
	{
		/* Optionally implemented by child class */
	}

	/**
	 * Runs when job regresses back through the workflow.
	 *
	 * @return mixed
	 */
	public function down()
	{
		/* Optionally implemented by child class */
	}
}

<?php namespace Tatter\Workflows;

use Tatter\Handlers\BaseHandler;
use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Models\ActionModel;

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
	public $attributes;

	/**
	 * @var array<string>|null
	 *
	 * @deprecated
	 */
	public $definition;

	/**
	 * @var \Tatter\Workflows\Config\Workflows
	 */
	public $config;

	/**
	 * @var \Tatter\Workflows\Entities\Job
	 */
	public $job;

	/**
	 * @var \Tatter\Workflows\Models\JobModel
	 */
	public $jobs;

	/**
	 * @var \CodeIgniter\HTTP\RequestInterface
	 */
	public $request;

	/**
	 * Sets up common resources for Actions.
	 *
	 * @return $this
	 */
	public function __construct()
	{
		$this->request = service('request');
		$this->config  = config('Workflows');
		$this->jobs    = model($this->config->jobModel);

		// Check for legacy definition
		if (is_null($this->attributes) && ! is_null($this->definition))
		{
			$this->attributes = $this->definition;
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Creates the database record for this class based on its definition.
	 *
	 * @return integer|boolean  int for inserted ID, true for existing entry, false for failure
	 */
	public function register()
	{
		$actions = model(ActionModel::class);

		// Check for an existing entry
		if ($action = $actions->where('uid', $this->attributes['uid'])->first())
		{
			return true;
		}

		return $actions->insert($this->toArray());
	}

	/**
	 * Deletes this action from the database (soft).
	 *
	 * @return boolean  Result from the model
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

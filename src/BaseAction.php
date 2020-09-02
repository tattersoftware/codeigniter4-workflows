<?php namespace Tatter\Workflows;

use Tatter\Workflows\Models\ActionModel;

/**
 * Class reference and common functions for all Actions.
 */
abstract class BaseAction
{
	/**
	 * @var array<string> Implemented by child class
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

		return $this;
	}

    /**
	 * Magic wrapper for getting values from the definition
	 *
	 * @param string $name
	 *
	 * @return string
	 */
    public function __get(string $name): string
    {
		return $this->definition[$name];
    }

	//--------------------------------------------------------------------

    /**
	 * Creates the database record for this class based on its definition
	 *
	 * @return int|bool  int for inserted ID, true for existing entry, false for failure
	 */
	public function register()
	{
		$actions = model(ActionModel::class);

		// Check for an existing entry
		if ($action = $actions->where('uid', $this->uid)->first())
		{
			return true;
		}

		return $actions->insert($this->definition);
	}

    /**
	 * Deletes this action from the database (soft)
	 *
	 * @return bool  Result from the model
	 */
	public function remove(): bool
	{
		return model(ActionModel::class)->where('uid', $this->uid)->delete();
	}

	//--------------------------------------------------------------------

    /**
	 * Runs when a job progresses forward through the workflow
	 *
	 * @return mixed
	 */
	public function up()
	{
		/* Optionally implemented by child class */
	}
	
    /**
	 * Runs when job regresses back through the workflow
	 *
	 * @return mixed
	 */
	public function down()
	{
		/* Optionally implemented by child class */
	}
}

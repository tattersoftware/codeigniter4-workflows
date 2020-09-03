<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\BaseAction;

class Action extends Entity
{
	protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Cached Action instance for "class" attribute.
     *
     * @var BaseAction
     */
    protected $instance;

    /**
     * Gets the associated Action instance
     *
     * @return BaseAction
     */
	public function getInstance(): BaseAction
	{
		if ($this->instance === null)
		{
			$this->instance = new $this->attributes['class']();
			$this->instance->initialize();
		}

		return $this->instance;
	}

    /**
     * Formulate the current route for this Action, with optional job
     * E.g.: return redirect()->to(site_url($action->route));
     *
	 * @param string|int|null $jobId
	 *
     * @return string
     */
	public function getRoute($jobId = null): string
	{
		$route = '/' . config('Workflows')->routeBase . '/' . $this->attributes['uid'];

		if ($jobId)
		{
			$route .= '/' . $jobId;
		}

		return $route;
	}

    /**
     * Checks if role filter is enabled and if the current user may access this action.
     *
     * @return bool
     */
	protected function mayAccess(): bool
	{
		// If role filtering is not set up then allow through
		if (! function_exists('has_permission'))
		{
			return true;
		}

		// Anyone can run user actions
		if (empty($this->attributes['role']) || $this->attributes['role'] === 'user')
		{
			return true;
		}

		// Otherwise check for Action role permission
		return has_permission($this->attributes['role']);
	}

	/**
     * Validates and runs the specified method from the instance.
	 *
	 * @param string $name
	 * @param array  $params
	 *
     * @return mixed  Result of the instance method
     * @throws WorkflowsException
     */
	public function __call(string $name, array $params)
	{
		// Make sure the instance supports the requested method
		$instance = $this->getInstance();
		if (! is_callable([$instance, $name]))
		{
			throw WorkflowsException::forUnsupportedActionMethod($this->attributes['name'], $name);
		}

		return $instance->$name();
	}
}

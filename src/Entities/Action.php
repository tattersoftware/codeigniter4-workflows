<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;
use Config\Services;
use Tatter\Users\Interfaces\HasPermission;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\BaseAction;

class Action extends Entity
{
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	/**
	 * Default set of attributes
	 */
	protected $attributes = [
		'role' => '',
	];

	/**
	 * Cached Action instance for "class" attribute.
	 *
	 * @var BaseAction|null
	 */
	private $instance;

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
		}

		return $this->instance;
	}

	/**
	 * Formulate the current route for this Action, with optional job
	 * E.g.: return redirect()->to(site_url($action->route));
	 *
	 * @param string|integer|null $jobId
	 *
	 * @return string
	 */
	public function getRoute($jobId = null): string
	{
		$route = '/' . config('Workflows')->routeBase . '/' . $this->attributes['uid'];

		if ($jobId !== null)
		{
			$route .= '/' . $jobId;
		}

		return $route;
	}

	/**
	 * Checks if role filter is enabled and if a user
	 * (defaults to current) may access this Action.
	 *
	 * @param HasPermission|null $user
	 *
	 * @return bool
	 */
	public function mayAccess(HasPermission $user = null): bool
	{
		// Anyone can run user actions
		if ($this->attributes['role'] === '')
		{
			return true;
		}

		// If no user was provided then get the current user
		if (is_null($user))
		{
			/** @var HasPermission|null $user */
			$user = Services::users()->findById(user_id());
		}

		// If still no user then deny
		if (is_null($user))
		{
			return false;
		}

		return $user->hasPermission($this->attributes['role']);
	}

	/**
	 * Validates and runs the specified method from the instance.
	 *
	 * @param string $name
	 * @param array  $params
	 *
	 * @return mixed Result of the instance method
	 *
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

		return $instance->$name(...$params);
	}
}

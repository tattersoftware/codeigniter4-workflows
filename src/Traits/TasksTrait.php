<?php namespace Tatter\Workflows\Traits;

use Tatter\Workflows\Models\TaskModel;

trait TasksTrait
{
	public $config;
	public $job;
	public $renderer;

	// Magic wrapper for getting values from the definition
    public function __get(string $name)
    {
		return $this->definition[$name];
    }

	// Create the database record of this task based on its definition
	public function register()
	{
		$tasks = new TaskModel();

		// Check for an existing entry
		if ($task = $tasks->where('uid', $this->uid)->first())
		{
			return true;
		}

		return $tasks->insert($this->definition, true);
	}

	// Soft delete this task from the database
	public function remove()
	{
		return (new TaskModel())->where('uid', $this->uid)->delete();
	}

	// Formulate the current route for this task & job
	// e.g.: return redirect()->to($this->route());
	public function route()
	{
		return '/' . $this->config->routeBase . '/' . $this->uid . '/' . $this->job->id;
	}
}

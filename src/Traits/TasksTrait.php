<?php namespace Tatter\Workflows\Traits;

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
		// Check for an existing entry
		if ($task = $this->model->where('uid', $this->uid)->first())
		{
			return true;
		}

		return $this->model->insert($this->definition, true);
	}

	// Soft delete this task from the database
	public function remove()
	{
		return $this->model->where('uid', $this->uid)->delete();
	}

	// Formulate the current route for this task & job
	// e.g.: return redirect()->to($this->route());
	public function route()
	{
		return '/' . $this->config->routeBase . '/' . $this->uid . '/' . $this->job->id;
	}
}

<?php namespace Tatter\Workflows\Traits;

use Tatter\Workflows\Models\ActionModel;

trait ActionsTrait
{
	public $config;
	public $job;
	public $renderer;

	// Magic wrapper for getting values from the definition
    public function __get(string $name)
    {
		return $this->definition[$name];
    }

	// Create the database record of this action based on its definition
	public function register()
	{
		$actions = new ActionModel();

		// Check for an existing entry
		if ($action = $actions->where('uid', $this->uid)->first())
		{
			return true;
		}

		return $actions->insert($this->definition, true);
	}

	// Soft delete this action from the database
	public function remove()
	{
		return (new ActionModel())->where('uid', $this->uid)->delete();
	}

	// Formulate the current route for this action & job
	// e.g.: return redirect()->to($this->route());
	public function route()
	{
		return '/' . $this->config->routeBase . '/' . $this->uid . '/' . $this->job->id;
	}
}

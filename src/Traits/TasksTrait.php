<?php namespace Tatter\Workflows\Traits;

use Tatter\Workflows\Models\TaskModel;

/*** CLASS ***/
trait TasksTrait
{
	public $job;
	
	public function __construct()
	{
		$this->model  = new TaskModel();
		$this->config = class_exists('\Config\Workflows') ?
			new \Config\Workflows() : new \Tatter\Workflows\Config\Workflows();
	}
	
	// magic wrapper for getting values from the definition
    public function __get(string $name)
    {
		return $this->definition[$name];
    }
	
	// create the database record of this task based on its definition
	public function register()
	{		
		// check for an existing entry
		if ($task = $this->model->where('uid', $this->uid)->first())
		{
			return true;
		}
		
		return $this->model->insert($this->definition, true);
	}
	
	// soft delete this task from the database
	public function remove()
	{
		return $this->model->where('uid', $this->uid)->delete();
	}
}

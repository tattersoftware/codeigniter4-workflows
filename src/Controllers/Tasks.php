<?php namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Config\Services;
use Tatter\Workflows\Entities\Task;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Models\WorkflowModel;

class Tasks extends Controller
{
	public function __construct()
	{
		$this->model  = new TaskModel();
		$this->config = class_exists('\Config\Workflows') ?
			new \Config\Workflows() : new \Tatter\Workflows\Config\Workflows();
	}
	
	public function get(...$params)
	{
		if (empty($params))
			redirect();
		
		$route = array_shift($params);
		$task = $this->model->where('uid', $route)->first();
		if (empty($task))
			die('Unable to locate that task');
		
		echo $task->class;

	}
	
	public function post(...$params)
	{
		var_dump($params);
	}
}

<?php namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Config\Services;
use Tatter\Workflows\Entities\Task;
use Tatter\Workflows\Models\StageModel;
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
	
	public function index()
	{
		$data['config'] = $this->config;
		$data['tasks'] = $this->model->orderBy('name')->findAll();
		
		return view('Tatter\Workflows\Views\tasks\index', $data);
	}
}

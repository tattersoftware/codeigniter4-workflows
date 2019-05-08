<?php namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Config\Services;
use Tatter\Workflows\Entities\Task;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Models\WorkflowModel;

class Workflows extends Controller
{
	public function __construct()
	{
		$this->model  = new WorkflowModel();
		$this->tasks  = new TaskModel();
		$this->config = class_exists('\Config\Workflows') ?
			new \Config\Workflows() : new \Tatter\Workflows\Config\Workflows();

		// get the library instance
		//$this->lib = Services::workflows();
	}
	
	public function index()
	{
		$data['workflows'] = $this->model->orderBy('name')->findAll();
		$data['config'] = $this->config;
		return view('Tatter\Workflows\Views\index', $data);
	}
	
	public function add()
	{
		$data['config'] = $this->config;
		return view('Tatter\Workflows\Views\add', $data);		
	}
}

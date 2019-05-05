<?php namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Config\Services;
use Tatter\Workflows\Entities\Task;
use Tatter\Workflows\Entities\Workflow;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Models\WorkflowModel;

class Workflows extends Controller
{
	public function __construct()
	{
		$this->model = new WorkflowModel();
		$this->tasks = new TaskModel();
		
		// get the library instance
		//$this->lib = Services::workflows();
		//$this->config = $this->users->getConfig();
		
		// start the session
		//$this->session = session();
	}
	
	public function index()
	{
		$data['workflows'] = $this->model->orderBy('name')->findAll();
		return view('Tatter\Workflows\Views\header');
	}
	
	public function add()
	{
		
	}
}

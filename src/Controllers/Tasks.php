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
		$this->config = config('Workflows');
	}
	
	public function index()
	{
		$data['layout'] = $this->config->layouts['manage'];
		$data['tasks']  = $this->model->orderBy('name')->findAll();
		
		return view('Tatter\Workflows\Views\tasks\index', $data);
	}
}

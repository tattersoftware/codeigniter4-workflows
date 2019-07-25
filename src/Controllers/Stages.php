<?php namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Config\Services;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\TaskModel;

class Stages extends Controller
{
	public function __construct()
	{
		$this->model  = new StageModel();
		$this->tasks  = new TaskModel();
		$this->config = config('Workflows');
	}
	
	public function update($stageId)
	{
		$row = $this->request->getPost();
		$result = $this->model->update($stageId, $row);
		if (! $result)
			echo 'Error: unable to update';
		return $result;
	}
}

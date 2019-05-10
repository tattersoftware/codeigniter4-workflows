<?php namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Config\Services;
use CodeIgniter\Exceptions\PageNotFoundException;

use Tatter\Workflows\Entities\Task;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Models\WorkflowModel;

class Runner extends Controller
{
	public function __construct()
	{
		$this->jobs       = new JobModel();
		$this->tasks      = new TaskModel();
		$this->workflows  = new WorkflowModel();
		
		$this->config = class_exists('\Config\Workflows') ?
			new \Config\Workflows() : new \Tatter\Workflows\Config\Workflows();
	}
	
	// receives route input and handles task coordination
	public function run(...$params)
	{
		if (empty($params))
			throw PageNotFoundException::forPageNotFound();
		
		// get route - mostly ornamental to make nice URLs
		$route = array_shift($params);
		$task = $this->tasks->where('uid', $route)->first();
		if (empty($task))
			die('Unable to locate that task');
		echo $task->class;
		
		// get the job ID
		$jobId = array_shift($params);
		if (empty($jobId))
			die('Job ID missing for {$route} task');
		
		// load the job
		$job = $this->jobs->find($jobId);
		if (empty($jobID))
			die('No job matched for ID #{$jobId}');
		
		// determine request method
		$request = Services::request();
		$method = $request->getMethod();
		
	}
}

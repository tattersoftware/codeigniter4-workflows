<?php namespace Tatter\Workflows\Tasks;

use Tatter\Workflows\Entities\Task;
use Tatter\Workflows\Interfaces\TaskInterface;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Models\WorkflowModel;

class WorkflowTask implements TaskInterface
{
	use \Tatter\Workflows\Traits\TasksTrait;
	
	public $definition = [
		'category' => 'Core',
		'name'     => 'Workflow',
		'uid'      => 'workflow',
		'input'    => 'workflow',
		'icon'     => 'fas fa-project-diagram',
		'summary'  => 'Run another workflow as a subordinate task',
	];
	
	public function get()
	{
	
	}
		
	// handle anything that needs to happen before this task can run
	// NOTE: called during job progression *and* regression
	public function init()
	{
		$this->model  = new WorkflowModel();
		$this->tasks  = new TaskModel();
		$this->config = config('Workflows');

		// get the library instance
		//$this->lib = Services::workflows();
	}
	
	// run when a job progresses forward through the workflow
	public function up()
	{
	
	}
	
	// run when job regresses back through the workflow
	public function down()
	{
	
	}
}

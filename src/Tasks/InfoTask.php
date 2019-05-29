<?php namespace Tatter\Workflows\Tasks;

use Tatter\Workflows\Entities\Task;
use Tatter\Workflows\Interfaces\TaskInterface;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Models\WorkflowModel;

class InfoTask implements TaskInterface
{
	use \Tatter\Workflows\Traits\TasksTrait;
	
	public $definition = [
		'category' => 'Core',
		'name'     => 'Info',
		'uid'      => 'info',
		'icon'     => 'fas fa-info-circle',
		'summary'  => 'Set basic details of a job',
	];
	
	// all tasks need an unqualified `get` method
	public function get()
	{
		print_r($this->definition);
		die('yes');
	}
	
	// handle anything that needs to happen before this task can run
	// NOTE: called during job progression *and* regression
	public function init()
	{
	
	}
	
	// run when job arrives while progressing through the workflow
	public function up()
	{
	
	}
	
	// run when job returns while regressing back through the workflow
	public function down()
	{
	
	}
	
	// handle anything that needs to happen before the task finishes
	public function finish()
	{
	
	}
}

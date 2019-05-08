<?php namespace Tatter\Workflows\Tasks;

use Tatter\Workflows\Entities\Task;
use Tatter\Workflows\Interfaces\TaskInterface;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Models\WorkflowModel;

class InfoTask implements TaskInterface
{
	use \Tatter\Workflows\Traits\TasksTrait;
	
	public $definition = [
		'name'     => 'Info',
		'category' => 'Core',
		'uid'      => 'info',
		'icon'     => 'fa-info-circle',
		'summary'  => 'Set basic details of a job',
	];
	
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
	
	// display a view or form for user interaction
	public function ui()
	{
	
	}
	
	// process user/form input
	public function process($data)
	{
	
	}
	
	// handle anything that needs to happen before the task finishes
	public function finalize()
	{
	
	}
}

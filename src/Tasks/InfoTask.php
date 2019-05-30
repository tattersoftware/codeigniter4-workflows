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
	
	public function get()
	{
		print_r($this->definition);
		die('yes');
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

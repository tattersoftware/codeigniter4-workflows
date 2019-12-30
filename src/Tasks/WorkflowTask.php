<?php namespace Tatter\Workflows\Tasks;

use Tatter\Workflows\Interfaces\TaskInterface;

class WorkflowTask implements TaskInterface
{
	use \Tatter\Workflows\Traits\TasksTrait;

	public $definition = [
		'category' => 'Core',
		'name'     => 'Workflow',
		'uid'      => 'workflow',
		'input'    => 'workflow',
		'role'     => 'user',
		'icon'     => 'fas fa-project-diagram',
		'summary'  => 'Run another workflow as a subordinate task',
	];

	public function get()
	{

	}

	// Run when a job progresses forward through the workflow
	public function up()
	{

	}

	// Run when job regresses back through the workflow
	public function down()
	{

	}
}

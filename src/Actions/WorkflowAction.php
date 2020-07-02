<?php namespace Tatter\Workflows\Actions;

use Tatter\Workflows\Interfaces\ActionInterface;

class WorkflowAction implements ActionInterface
{
	use \Tatter\Workflows\Traits\ActionsTrait;

	public $definition = [
		'category' => 'Core',
		'name'     => 'Workflow',
		'uid'      => 'workflow',
		'input'    => 'workflow',
		'role'     => 'user',
		'icon'     => 'fas fa-project-diagram',
		'summary'  => 'Run another workflow as a subordinate action',
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

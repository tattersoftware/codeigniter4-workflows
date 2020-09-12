<?php namespace Tatter\Workflows\Actions;

use Tatter\Workflows\BaseAction;

class WorkflowAction extends BaseAction
{
	public $attributes = [
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
		throw new \RuntimeException('Not yet implemented');
	}
}

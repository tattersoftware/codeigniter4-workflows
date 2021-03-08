<?php namespace Tatter\Workflows\Actions;

use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Workflows\BaseAction;
use RuntimeException;

class WorkflowAction extends BaseAction
{
	public $attributes = [
		'category' => 'Core',
		'name'     => 'Workflow',
		'uid'      => 'workflow',
		'input'    => 'workflow',
		'role'     => '',
		'icon'     => 'fas fa-project-diagram',
		'summary'  => 'Run another workflow as a subordinate action',
	];

	public function get(): ?ResponseInterface
	{
		throw new RuntimeException('Not yet implemented');
	}
}

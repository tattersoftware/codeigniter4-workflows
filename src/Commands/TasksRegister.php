<?php namespace Tatter\Workflows\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;
use Tatter\Workflows\Registrar;

class TasksRegister extends BaseCommand
{
    protected $group       = 'Workflows';
    protected $name        = 'tasks:register';
    protected $description = 'Search for new tasks and add them to the database';
    
	protected $usage     = 'tasks:register';
	protected $arguments = [];

	public function run(array $params = [])
    {
		$count = Registrar::tasks();

		if ($count === 0)
		{
			CLI::write('No new tasks found in any namespace.', 'yellow');
			return;
		}
		
		$this->call('tasks:list');
	}
}

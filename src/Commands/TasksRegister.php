<?php namespace Tatter\Workflows\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Services;
use Tatter\Workflows\Models\TaskModel;

class TasksRegister extends BaseCommand
{
    protected $group       = 'Workflows';
    protected $name        = 'tasks:register';
    protected $description = 'Search for new tasks and add them to the database';
    
	protected $usage     = 'tasks:register';
	protected $arguments = [ ];

	public function run(array $params = [])
    {
		$tasks = new TaskModel();
		$locator = Services::locator(true);

		// get all namespaces from the autoloader
		$namespaces = Services::autoloader()->getNamespace();
		
		// scan each namespace for tasks
		$flag = false;
		foreach ($namespaces as $namespace => $paths):

			// get any files in /Tasks/ for this namespace
			$files = $locator->listNamespaceFiles($namespace, '/Tasks/');
			foreach ($files as $file):
			
				// skip non-PHP files
				if (substr($file, -4) !== '.php'):
					continue;
				endif;
				
				// get namespaced class name
				$name = basename($file, '.php');
				$class = $namespace . '\Tasks\\' . $name;
				
				include_once $file;

				// validate the class
				if (! class_exists($class, false)):
					throw new \RuntimeException("Could not locate {$class} in {$file}");
				endif;
				$instance = new $class();
				
				// validate the method
				if (! is_callable([$instance, 'register'])):
					throw new \RuntimeException("Missing 'register' method for {$class} in {$file}");
				endif;
				
				// register it
				$result = $instance->register();
				
				// if this was a new registration, add the namespaced class
				if (is_int($result)):
					$flag = true;
					
					$task = $tasks->find($result);
					$task->class = $class;
					$tasks->save($task);
				
					CLI::write("Registered {$task->name} from {$class}", 'green');
				endif;
				
			endforeach;
		endforeach;
		
		if ($flag == false):
			CLI::write('No new tasks found in any namespace.', 'yellow');
			return;
		endif;
		
		$this->call('tasks:list');
	}
}

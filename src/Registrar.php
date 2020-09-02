<?php namespace Tatter\Workflows;

use CodeIgniter\CLI\CLI;
use Config\Services;
use Tatter\Workflows\Models\ActionModel;

/**
 * Class to handle Action registration.
 */
class Registrar
{
	/**
	 * Scan all namespaces for new actions to laod into the database
	 *
	 * @return int  Number of new actions registered
	 */
	static public function actions(): int
    {
		$actions = model(ActionModel::class);
		$locator = Services::locator(true);

		// Get all namespaces from the autoloader
		$namespaces = Services::autoloader()->getNamespace();
		
		// Scan each namespace for actions
		$count = 0;
		foreach ($namespaces as $namespace => $paths)
		{
			// Get any files in Actions/ for this namespace
			$files = $locator->listNamespaceFiles($namespace, '/Actions/');
			
			foreach ($files as $file)
			{
				// Skip non-PHP files
				if (substr($file, -4) !== '.php')
				{
					continue;
				}
				
				// Get namespaced class name
				$name  = basename($file, '.php');
				$class = $namespace . '\Actions\\' . $name;
				
				include_once $file;

				// Validate the class
				if (! class_exists($class, false))
				{
					throw new \RuntimeException("Could not locate {$class} in {$file}");
				}
				$instance = new $class();
				
				// Validate the method
				if (! is_callable([$instance, 'register']))
				{
					throw new \RuntimeException("Missing 'register' method for {$class} in {$file}");
				}
				
				// Register it
				$result = $instance->register();
				
				// If this was a new registration, add the namespaced class
				if (is_int($result))
				{
					$count++;
					
					$action = $actions->find($result);
					$action->class = $class;
					$actions->save($action);
				
					if (ENVIRONMENT !== 'testing' && is_cli())
					{
						CLI::write("Registered {$action->name} from {$class}", 'green');
					}
				}
			}
		}
		
		return $count;
	}
}

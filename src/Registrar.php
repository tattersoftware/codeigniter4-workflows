<?php namespace Tatter\Workflows;

use CodeIgniter\CLI\CLI;
use Tatter\Handlers\Handlers;
use Tatter\Workflows\Models\ActionModel;
use RuntimeException;

/**
 * Class to register Actions in the database.
 */
class Registrar
{
	/**
	 * Scans all namespaces for new Actions to load into the database
	 *
	 * @return int Number of new Actions registered
	 */
	public static function actions(): int
	{
		$model    = model(ActionModel::class);
		$handlers = new Handlers('Actions');

		$count = 0;
		foreach ($handlers->all() as $class)
		{
			$instance = new $class();

			// Validate the method
			if (! is_callable([$instance, 'register']))
			{
				throw new RuntimeException("Missing 'register' method for {$class}");
			}

			// Register it
			$result = $instance->register();

			$count++;
		}

		return $count;
	}
}

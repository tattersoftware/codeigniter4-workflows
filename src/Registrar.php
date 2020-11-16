<?php namespace Tatter\Workflows;

use CodeIgniter\CLI\CLI;
use Tatter\Handlers\Handlers;
use Tatter\Workflows\Models\ActionModel;

/**
 * Class to register Actions in the database.
 */
class Registrar
{
	/**
	 * Scans all namespaces for new Actions to load into the database
	 *
	 * @return integer  Number of new Actions registered
	 */
	static public function actions(): int
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
				throw new \RuntimeException("Missing 'register' method for {$class}");
			}

			// Register it
			$result = $instance->register();

			// If this was a new registration, add the namespaced class
			if (is_int($result))
			{
				$model->update($result, ['class' => $class]);

				if (ENVIRONMENT !== 'testing' && is_cli())
				{
					CLI::write("Registered {$class}", 'green');
				}

				$count++;
			}
		}

		return $count;
	}
}

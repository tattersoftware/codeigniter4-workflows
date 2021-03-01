<?php namespace Tatter\Workflows\Test;

use CodeIgniter\Test\Fabricator;
use Tatter\Workflows\Registrar;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\WorkflowModel;

/**
 * Support class for simulating a complete workflow environment.
 */
class Simulator
{
	/**
	 * Whether initialize() has been run
	 *
	 * @var boolean
	 */
	static public $initialized = false;

	/**
	 * Initialize the simulation.
	 *
	 * @param array $targets Array of target items to create
	 */
	static public function initialize($targets = ['actions', 'jobs', 'stages', 'workflows'])
	{
		self::reset();

		// Register any Actions and update the count
		if (in_array('actions', $targets))
		{
			Fabricator::setCount('actions', Registrar::actions());

			// Create actions up to N
			$count = rand(10, 20);
			while (Fabricator::getCount('actions') < $count)
			{
				fake(ActionModel::class);
			}
		}

		// Create workflows up to N
		if (in_array('workflows', $targets))
		{
			$count = rand(2, 7);
			while (Fabricator::getCount('workflows') < $count)
			{
				fake(WorkflowModel::class);
			}
		}

		// Create stages up to N
		if (in_array('stages', $targets))
		{
			$count = Fabricator::getCount('workflows') * rand(4, 8);
			while (Fabricator::getCount('stages') < $count)
			{
				fake(StageModel::class);
			}
		}

		// Create jobs up to N
		if (in_array('jobs', $targets))
		{
			$count = rand(40, 200);
			while (Fabricator::getCount('jobs') < $count)
			{
				fake(JobModel::class);
			}
		}

		self::$initialized = true;
	}

	/**
	 * Reset counts.
	 */
	static public function reset()
	{
		// Reset counts on faked items
		Fabricator::resetCounts();

		self::$initialized = false;
	}
}

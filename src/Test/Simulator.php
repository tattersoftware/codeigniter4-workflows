<?php namespace Tatter\Workflows\Test;

use CodeIgniter\Test\Fabricator;
use Tatter\Workflows\Registrar;
use Tatter\Workflows\Test\Fakers\JobFaker;
use Tatter\Workflows\Test\Fakers\StageFaker;
use Tatter\Workflows\Test\Fakers\ActionFaker;
use Tatter\Workflows\Test\Fakers\WorkflowFaker;

/**
 * Support class for simulating a complete workflow environment.
 */
class Simulator
{
	/**
	 * Whether initialize() has been run
	 *
	 * @var array
	 */
	static public $initialized = false;

	/**
	 * LEGACY - Replaced by Fabricator::counts
	 *
	 * @var array
	 * @deprecated 2.0.2
	 */
	static public $counts = [
		'jobs'      => 0,
		'stages'    => 0,
		'actions'   => 0,
		'workflows' => 0,
	];

	/**
	 * Initialize the simulation.
	 *
	 * @param array  Array of target items to create
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
				fake(ActionFaker::class);
			}
		}

		// Create workflows up to N
		if (in_array('workflows', $targets))
		{
			$count = rand(2, 7);
			while (Fabricator::getCount('workflows') < $count)
			{
				fake(WorkflowFaker::class);
			}
		}
		
		// Create stages up to N
		if (in_array('stages', $targets))
		{
			$count = Fabricator::getCount('workflows') * rand(4, 8);
			while (Fabricator::getCount('stages') < $count)
			{
				fake(StageFaker::class);
			}
		}

		// Create jobs up to N
		if (in_array('jobs', $targets))
		{
			$count = rand(40, 200);
			while (Fabricator::getCount('jobs') < $count)
			{
				fake(JobFaker::class);
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

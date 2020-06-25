<?php namespace Tatter\Workflows\Test;

use Tatter\Workflows\Registrar;
use Tatter\Workflows\Test\Fakers\JobFaker;
use Tatter\Workflows\Test\Fakers\StageFaker;
use Tatter\Workflows\Test\Fakers\TaskFaker;
use Tatter\Workflows\Test\Fakers\WorkflowFaker;

/**
 * Support class for simulating a complete workflow environment.
 */
class Simulator
{
	/**
	 * Number of each object type created since last reset.
	 *
	 * @var array
	 */
	static public $counts = [
		'jobs'      => 0,
		'stages'    => 0,
		'tasks'     => 0,
		'workflows' => 0,
	];

	/**
	 * Initialize the simulation.
	 */
	static public function initialize()
	{
		self::reset();

		// Register any Tasks and update the count
		self::$counts['tasks'] = Registrar::tasks();

		// Create tasks up to N
		$count = rand(10, 20);
		while (self::$counts['tasks'] < $count)
		{
			fake(TaskFaker::class);
		}

		// Create workflows up to N
		$count = rand(2, 7);
		while (self::$counts['workflows'] < $count)
		{
			fake(WorkflowFaker::class);
		}

		// Create stages up to N
		$count = self::$counts['workflows'] * rand(4, 8);
		while (self::$counts['stages'] < $count)
		{
			fake(StageFaker::class);
		}

		// Create jobs up to N
		$count = rand(40, 200);
		while (self::$counts['jobs'] < $count)
		{
			fake(JobFaker::class);
		}
	}

	/**
	 * Reset counts.
	 */
	static public function reset()
	{
		foreach (self::$counts as $key => $val)
		{
			self::$counts[$key] = 0;
		}
	}
}

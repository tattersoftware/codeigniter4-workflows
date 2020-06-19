<?php namespace Tatter\Workflows\Test;

use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Models\WorkflowModel;

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

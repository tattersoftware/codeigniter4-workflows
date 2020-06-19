<?php namespace Tatter\Workflows\Test\Fakers;

use Faker\Generator;
use Tatter\Workflows\Entities\Task;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Test\Simulator;

class TaskFaker extends TaskModel
{
	/**
	 * Faked data for Fabricator.
	 *
	 * @param Generator $faker
	 *
	 * @return Task
	 */
	public function fake(Generator &$faker)
	{
		Simulator::$counts['tasks']++;

		$name = $faker->word;

		return new Task([
			'category'    => $faker->streetSuffix,
			'name'        => ucfirst($name),
			'uid'         => strtolower($name),
			'class'       => implode('\\', array_map('ucfirst', self::$faker->words)),
			'role'        => rand(0, 2) ? 'user' : 'admin',
			'icon'        => $faker->safeColorName,
			'summary'     => $faker->sentence,
			'description' => $faker->paragraph,
		]);
	}
}

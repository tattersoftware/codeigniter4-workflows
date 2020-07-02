<?php namespace Tatter\Workflows\Test\Fakers;

use Faker\Generator;
use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Test\Simulator;

class StageFaker extends StageModel
{
	/**
	 * Faked data for Fabricator.
	 *
	 * @param Generator $faker
	 *
	 * @return Stage
	 */
	public function fake(Generator &$faker)
	{
		Simulator::$counts['stages']++;

		$name = $faker->word;

		return new Stage([
			'action_id'     => rand(1, Simulator::$counts['actions']     ?: 12),
			'workflow_id' => rand(1, Simulator::$counts['workflows'] ?: 4),
			'required'    => (bool) rand(0, 5),
		]);
	}
}

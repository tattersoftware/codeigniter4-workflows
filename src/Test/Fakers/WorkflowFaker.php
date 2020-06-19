<?php namespace Tatter\Workflows\Test\Fakers;

use Faker\Generator;
use Tatter\Workflows\Entities\Workflow;
use Tatter\Workflows\Models\WorkflowModel;
use Tatter\Workflows\Test\Simulator;

class WorkflowFaker extends WorkflowModel
{
	/**
	 * Faked data for Fabricator.
	 *
	 * @param Generator $faker
	 *
	 * @return Workflow
	 */
	public function fake(Generator &$faker)
	{
		Simulator::$counts['workflows']++;

		return new Workflow([
			'name'        => $faker->word,
			'category'    => $faker->streetSuffix,
			'icon'        => $faker->safeColorName,
			'summary'     => $faker->sentence,
			'description' => $faker->paragraph,
		]);
	}
}

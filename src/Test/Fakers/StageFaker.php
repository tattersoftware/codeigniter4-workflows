<?php namespace Tatter\Workflows\Test\Fakers;

use CodeIgniter\Test\Fabricator;
use Faker\Generator;
use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Models\StageModel;

class StageFaker extends StageModel
{
	/**
	 * Faked data for Fabricator.
	 *
	 * @param Generator $faker
	 *
	 * @return Stage
	 */
	public function fake(Generator &$faker): Stage
	{
		return new Stage([
			'action_id'   => rand(1, Fabricator::getCount('actions') ?: 12),
			'workflow_id' => rand(1, Fabricator::getCount('workflows') ?: 4),
			'required'    => (bool) rand(0, 5),
		]);
	}
}

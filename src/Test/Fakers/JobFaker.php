<?php namespace Tatter\Workflows\Test\Fakers;

use CodeIgniter\Test\Fabricator;
use Faker\Generator;
use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Models\JobModel;

class JobFaker extends JobModel
{
	/**
	 * Faked data for Fabricator.
	 *
	 * @param Generator $faker
	 *
	 * @return Job
	 */
	public function fake(Generator &$faker): Job
	{
		return new Job([
			'name'        => $faker->catchPhrase,
			'summary'     => $faker->sentence,
			'workflow_id' => rand(1, Fabricator::getCount('workflows') ?: 4),
			'stage_id'    => rand(1, Fabricator::getCount('stages') ?: 99),
		]);
	}
}

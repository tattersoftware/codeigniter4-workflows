<?php namespace Tatter\Workflows\Test\Fakers;

use Faker\Generator;
use Tatter\Workflows\Entities\Action;
use Tatter\Workflows\Models\ActionModel;

class ActionFaker extends ActionModel
{
	/**
	 * Faked data for Fabricator.
	 *
	 * @param Generator $faker
	 *
	 * @return Action
	 */
	public function fake(Generator &$faker): Action
	{
		$name = $faker->word;

		return new Action([
			'category'    => $faker->streetSuffix,
			'name'        => ucfirst($name),
			'uid'         => strtolower($name),
			'class'       => implode('\\', array_map('ucfirst', $faker->words)),
			'role'        => rand(0, 2) ? 'user' : 'admin',
			'icon'        => $faker->safeColorName,
			'summary'     => $faker->sentence,
			'description' => $faker->paragraph,
		]);
	}
}

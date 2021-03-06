<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;
use Faker\Generator;
use Tatter\Workflows\Entities\Action;

class ActionModel extends Model
{
	protected $table          = 'actions';
	protected $returnType     = Action::class;
	protected $useSoftDeletes = true;
	protected $useTimestamps  = true;
	protected $allowedFields  = [
		'category', 'name', 'uid', 'class', 'input',
		'role', 'icon', 'summary', 'description',
	];

	protected $validationRules = [
		'category' => 'required|max_length[63]',
		'name'     => 'required|max_length[63]',
		'uid'      => 'required|max_length[63]',
		'class'    => 'permit_empty|max_length[63]',
		'input'    => 'permit_empty|max_length[63]',
		'role'     => 'permit_empty|max_length[63]',
		'icon'     => 'permit_empty|max_length[63]',
		'summary'  => 'permit_empty|max_length[255]',
	];

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
			'role'        => rand(0, 2) ? '' : 'admin',
			'icon'        => $faker->safeColorName,
			'summary'     => $faker->sentence,
			'description' => $faker->paragraph,
		]);
	}
}

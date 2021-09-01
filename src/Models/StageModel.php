<?php

namespace Tatter\Workflows\Models;

use CodeIgniter\Model;
use CodeIgniter\Test\Fabricator;
use Faker\Generator;
use Tatter\Workflows\Entities\Stage;

class StageModel extends Model
{
	use \Tatter\Audits\Traits\AuditsTrait;

	protected $table = 'stages';

	protected $returnType = Stage::class;

	protected $useTimestamps = true;

	protected $allowedFields = [
		'action_id', 'workflow_id', 'input', 'required',
	];

	protected $validationRules = [
		'action_id'   => 'required|is_natural_no_zero',
		'workflow_id' => 'required|is_natural_no_zero',
		'input'       => 'permit_empty|max_length[63]',
	];

	// Tatter\Audits
	protected $afterInsert = ['auditInsert'];

	protected $afterUpdate = ['auditUpdate'];

	protected $afterDelete = ['auditDelete'];

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
			'action_id'   => mt_rand(1, Fabricator::getCount('actions') ?: 12),
			'workflow_id' => mt_rand(1, Fabricator::getCount('workflows') ?: 4),
			'required'    => mt_rand(0, 5) ? 1 : 0,
		]);
	}
}

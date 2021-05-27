<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;
use CodeIgniter\Test\Fabricator;
use Faker\Generator;
use Tatter\Workflows\Entities\Action;
use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Entities\Workflow;

class StageModel extends Model
{
	use \Tatter\Audits\Traits\AuditsTrait;
	
	protected $table          = 'stages';
	protected $returnType     = Stage::class;
	protected $useTimestamps = true;
	protected $allowedFields = [
		'action_id', 'workflow_id', 'input', 'required',
	];

	protected $validationRules = [
		'action_id'   => 'required|is_natural_no_zero',
		'workflow_id' => 'required|is_natural_no_zero',
	];
	
	/*** Tatter\Audits ***/
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
			'action_id'   => rand(1, Fabricator::getCount('actions') ?: 12),
			'workflow_id' => rand(1, Fabricator::getCount('workflows') ?: 4),
			'required'    => rand(0, 5) ? 1 : 0,
		]);
	}
}

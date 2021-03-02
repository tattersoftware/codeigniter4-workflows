<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;
use Faker\Generator;
use CodeIgniter\Test\Fabricator;

class ExplicitModel extends Model
{
	use \Tatter\Audits\Traits\AuditsTrait;
	
	protected $table          = 'users_workflows';
	protected $returnType     = 'object';
	protected $useSoftDeletes = false;
	protected $useTimestamps  = true;
	protected $updatedField   = '';
	protected $allowedFields  = [
		'user_id', 'workflow_id', 'permitted',
	];

	protected $validationRules    = [
		'user_id'     => 'required|is_natural_no_zero',
		'workflow_id' => 'required|is_natural_no_zero',
		'permitted'   => 'required',
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
	 * @return object
	 */
	public function fake(Generator &$faker): object
	{
		return (object) [
			'user_id'     => rand(1, Fabricator::getCount('users') ?: 10),
			'workflow_id' => rand(1, Fabricator::getCount('workflows') ?: 4),
			'permitted'   => (bool) rand(0,4),
		];
	}
}

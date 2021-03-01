<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;
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
}

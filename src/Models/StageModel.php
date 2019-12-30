<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;

class StageModel extends Model
{
	use \Tatter\Audits\Traits\AuditsTrait;
	
	protected $table          = 'stages';
	protected $returnType     = 'Tatter\Workflows\Entities\Stage';
	protected $useTimestamps = true;
	protected $allowedFields = [
		'task_id', 'workflow_id', 'input', 'required',
	];
	
	/*** Tatter\Audits ***/
	protected $afterInsert = ['auditInsert'];
	protected $afterUpdate = ['auditUpdate'];
	protected $afterDelete = ['auditDelete'];
}

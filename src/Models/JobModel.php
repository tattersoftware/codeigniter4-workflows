<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;

class JobModel extends Model
{
	protected $table      = 'workflows';
	protected $primaryKey = 'id';

	protected $returnType = 'Tatter\Workflows\Entities\Job';
	protected $useSoftDeletes = true;

	protected $allowedFields = [
		'name', 'source', 'workflow_id', 'task_id',
		'summary', 'description',
	];

	protected $useTimestamps = true;

	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;
}

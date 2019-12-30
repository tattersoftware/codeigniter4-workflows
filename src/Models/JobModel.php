<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;

class JobModel extends Model
{
	use \Tatter\Workflows\Traits\JobsTrait;

	protected $table          = 'jobs';
	protected $returnType     = 'Tatter\Workflows\Entities\Job';
	protected $useSoftDeletes = true;
	protected $useTimestamps  = true;
	protected $allowedFields  = [
		'name', 'summary', 'workflow_id', 'stage_id',
	];

	protected $validationRules = [
		'name' => 'required|max_length[255]',
	];
	
	protected $afterInsert  = ['logInsert'];
	protected $beforeUpdate = ['logUpdate'];
}

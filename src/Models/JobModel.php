<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;
use Tatter\Workflows\Entities\Job;

class JobModel extends Model
{
	use \Tatter\Workflows\Traits\JobsTrait;

	protected $table          = 'jobs';
	protected $returnType     = Job::class;
	protected $useSoftDeletes = true;
	protected $useTimestamps  = true;
	protected $allowedFields  = [
		'name', 'summary', 'workflow_id', 'stage_id',
	];

	protected $validationRules = [
		'name'        => 'required|max_length[255]',
		'workflow_id' => 'required|is_natural_no_zero',
		'stage_id'    => 'permit_empty|is_natural_no_zero',
	];
	
	protected $afterInsert  = ['logInsert'];
	protected $beforeUpdate = ['logUpdate'];
}

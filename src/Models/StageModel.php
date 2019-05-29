<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;

class StageModel extends Model
{
	protected $table      = 'stages';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $useSoftDeletes = false;

	protected $allowedFields = [
		'task_id', 'workflow_id', 'input', 'required',
	];

	protected $useTimestamps = true;

	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;
}

<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;

class WorkflowModel extends Model
{
	protected $table      = 'workflows';
	protected $primaryKey = 'id';

	protected $returnType = 'object';
	protected $useSoftDeletes = true;

	protected $allowedFields = [
		'name', 'category', 'icon', 'summary', 'description'
	];

	protected $useTimestamps = true;

	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;
}

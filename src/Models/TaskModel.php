<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
	protected $table      = 'tasks';
	protected $primaryKey = 'id';

	protected $returnType = 'Tatter\Workflows\Entities\Task';
	protected $useSoftDeletes = true;

	protected $allowedFields = [
		'category', 'name', 'uid', 'class',
		'icon', 'summary', 'description',
	];

	protected $useTimestamps = true;

	protected $validationRules    = [];
	protected $validationMessages = [];
	protected $skipValidation     = false;
}


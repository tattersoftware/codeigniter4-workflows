<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
	protected $table          = 'tasks';
	protected $returnType     = 'Tatter\Workflows\Entities\Task';
	protected $useSoftDeletes = true;
	protected $useTimestamps  = true;
	protected $allowedFields  = [
		'category', 'name', 'uid', 'class', 'input',
		'role', 'icon', 'summary', 'description',
	];
}

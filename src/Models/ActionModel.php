<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;

class ActionModel extends Model
{
	protected $table          = 'actions';
	protected $returnType     = 'Tatter\Workflows\Entities\Action';
	protected $useSoftDeletes = true;
	protected $useTimestamps  = true;
	protected $allowedFields  = [
		'category', 'name', 'uid', 'class', 'input',
		'role', 'icon', 'summary', 'description',
	];
}

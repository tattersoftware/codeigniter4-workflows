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

	protected $validationRules = [
		'category' => 'required|max_length[255]',
		'name'     => 'required|max_length[255]',
		'uid'      => 'required|max_length[255]',
		'class'    => 'required|max_length[255]',
	];
}

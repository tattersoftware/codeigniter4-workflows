<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;
use Tatter\Workflows\Entities\Action;

class ActionModel extends Model
{
	protected $table          = 'actions';
	protected $returnType     = Action::class;
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
	];
}

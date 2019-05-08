<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;

class Task extends Entity
{
	protected $id;
	protected $category;
	protected $name;
	protected $uid;
	protected $class;
	protected $icon;
	protected $summary;
	protected $description;
	protected $deleted;
	protected $created_at;
	protected $updated_at;
	
	protected $_options = [
		'dates' => ['created_at', 'updated_at'],
		'casts' => [],
		'datamap' => []
	];
}

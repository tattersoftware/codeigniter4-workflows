<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;

class Job extends Entity
{
	protected $id;
	protected $name;
	protected $source;
	protected $workflow_id;
	protected $task_id;
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

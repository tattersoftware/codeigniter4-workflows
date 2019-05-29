<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;

class JobModel extends Model
{
	protected $table      = 'jobs';
	protected $primaryKey = 'id';

	protected $returnType = 'Tatter\Workflows\Entities\Job';
	protected $useSoftDeletes = true;

	protected $allowedFields = [
		'name', 'summary', 'workflow_id', 'stage_id',
	];

	protected $useTimestamps = true;

	protected $validationRules    = [
		'name'     => 'required|max_length[255]',
	];
	protected $validationMessages = [];
	protected $skipValidation     = false;
	
	protected $afterInsert = ['logInsert'];
	
	// log successful insertions
	protected function logInsert(array $data)
	{
		if (! $data['result'])
			return false;
		
		// determine user source from config
		$config = class_exists('\Config\Workflows') ?
			new \Config\Workflows() : new \Tatter\Workflows\Config\Workflows();
		$userId = session($config->userSource);
		
		// build the row
		$row = [
			'job_id'     => $data['result']->connID->insert_id,
			'stage_to'   => $data['data']['stage_id'],
			'created_by' => $userId,
			'created_at' => date('Y-m-d H:i:s'),
		];
		
		// add it to the database
		$db = db_connect();
		$db->table('joblogs')->insert($row);
	}
}

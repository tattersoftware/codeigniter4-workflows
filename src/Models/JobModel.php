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
	protected $afterUpdate = ['logUpdate'];
	
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
	
	// log updates that result in a stage change
	protected function logUpdate(array $data)
	{
		if (! $data['result'])
			return false;

		// determine user source from config
		$config = class_exists('\Config\Workflows') ?
			new \Config\Workflows() : new \Tatter\Workflows\Config\Workflows();
		$userId = session($config->userSource);
		
		// process each updated entry
		foreach ($data['id'] as $id):
			// get the updated job
			$job = $this->find($id);

			// ignore instances where the stage didn't change
			if (! isset($data['data']['stage_id']) || $data['data']['stage_id'] == $job->stage_id)
				continue;

			// build the row
			$row = [
				'job_id'     => $job->id,
				'stage_from' => $data['data']['stage_id'],
				'stage_to'   => $job->stage_id,
				'created_by' => $userId,
				'created_at' => date('Y-m-d H:i:s'),
			];
		
			// add it to the database
			$db = db_connect();
			$db->table('joblogs')->insert($row);
		endforeach;
	}
}

<?php namespace Tatter\Workflows\Traits;

trait JobsTrait
{
	// Log successful insertions
	protected function logInsert(array $data)
	{
		if (! $data['result'])
		{
			return false;
		}

		// Determine user source from config
		$userId = session(config('Workflows')->userSource);

		// Build the row
		$row = [
			'job_id'     => $data['id'],
			'stage_to'   => $data['data']['stage_id'],
			'created_by' => $userId,
			'created_at' => date('Y-m-d H:i:s'),
		];

		// Add it to the database
		$db = db_connect();
		$db->table('joblogs')->insert($row);

		return $data;
	}
	
	// Log updates that result in a stage change
	protected function logUpdate(array $data)
	{
		$db = db_connect();

		// Determine user source from config
		$userId = session(config('Workflows')->userSource);
		
		// Process each updated entry
		foreach ($data['id'] as $id)
		{
			// Get the job to be updated
			$job = $this->find($id);
			if (empty($job))
			{
				continue;
			}

			// Ignore when the stage will not be not touched
			if (! in_array('stage_id', array_keys($data['data'])))
			{
				continue;
			}
			
			// Ignore when the stage is the same
			if ($data['data']['stage_id'] == $job->stage_id)
			{
				continue;
			}

			// Build the row
			$row = [
				'job_id'     => $job->id,
				'stage_from' => $job->stage_id,
				'stage_to'   => $data['data']['stage_id'],
				'created_by' => $userId,
				'created_at' => date('Y-m-d H:i:s'),
			];
		
			// Add it to the database
			$db->table('joblogs')->insert($row);
		}
		
		return $data;
	}
}

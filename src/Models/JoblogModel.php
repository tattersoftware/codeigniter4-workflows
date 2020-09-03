<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;

class JoblogModel extends Model
{
	protected $table      = 'joblogs';
	protected $primaryKey = 'id';
	protected $returnType = 'Tatter\Workflows\Entities\Joblog';

	protected $useTimestamps  = true;
	protected $updatedField   = '';
	protected $useSoftDeletes = false;
	protected $skipValidation = true;

	protected $allowedFields  = ['job_id', 'stage_from', 'stage_to', 'user_id'];

    /**
     * Returns all logs for a job seeded with their "from" and "to" stages
     *
     * @param int $jobId  Job ID
     *
     * @return array|null
     */
	public function findWithStages(int $jobId): ?array
	{
		$logs = $this->where('job_id', $jobId)->orderBy('created_at', 'asc')->findAll();
		if (empty($logs))
		{
			return null;
		}

		// Determine the stages we need
		$stageIds = array_column($logs, 'stage_from') + array_column($logs, 'stage_to');
		
		// Get the stages and store them by their ID
		$stages = [];
		foreach ((new StageModel)->find($stageIds) as $stage)
		{
			$stages[$stage->id] = $stage;
		}
		
		// Inject the stages
		foreach ($logs as $i => $log)
		{
			$logs[$i]->from = $stages[$log->stage_from] ?? null;
			$logs[$i]->to   = $stages[$log->stage_to] ?? null;
		}

		return $logs;
	}
}

<?php

namespace Tatter\Workflows\Models;

use CodeIgniter\Model;
use CodeIgniter\Test\Fabricator;
use Faker\Generator;
use Tatter\Workflows\Entities\Joblog;

class JoblogModel extends Model
{
    protected $table = 'joblogs';

    protected $primaryKey = 'id';

    protected $returnType = Joblog::class;

    protected $useTimestamps = true;

    protected $updatedField = '';

    protected $useSoftDeletes = false;

    protected $allowedFields = ['job_id', 'stage_from', 'stage_to', 'user_id'];

    protected $validationRules = [
        'job_id'     => 'required|is_natural_no_zero',
        'stage_from' => 'permit_empty|is_natural_no_zero',
        'stage_to'   => 'permit_empty|is_natural_no_zero',
    ];

    /**
     * Returns all logs for a job seeded with their "from" and "to" stages.
     *
     * @param int $jobId Job ID
     *
     * @return array|null
     */
    public function findWithStages(int $jobId): ?array
    {
        $logs = $this->where('job_id', $jobId)->orderBy('created_at', 'asc')->findAll();
        if (empty($logs)) {
            return null;
        }

        // Determine the stages we need
        $stageIds = array_column($logs, 'stage_from') + array_column($logs, 'stage_to');

        // Get the stages and store them by their ID
        $stages = [];
        foreach ((new StageModel())->find($stageIds) as $stage) {
            $stages[$stage->id] = $stage;
        }

        // Inject the stages
        foreach ($logs as $i => $log) {
            $logs[$i]->from = $stages[$log->stage_from] ?? null;
            $logs[$i]->to   = $stages[$log->stage_to] ?? null;
        }

        return $logs;
    }

    /**
     * Faked data for Fabricator.
     *
     * @param Generator $faker
     *
     * @return Joblog
     */
    public function fake(Generator &$faker): Joblog
    {
        return new Joblog([
            'job_id'     => mt_rand(1, Fabricator::getCount('jobs') ?: 5),
            'stage_from' => mt_rand(1, Fabricator::getCount('stages') ?: 10),
            'stage_to'   => mt_rand(1, Fabricator::getCount('stages') ?: 10),
        ]);
    }
}

<?php

namespace Tatter\Workflows\Models;

use CodeIgniter\Model;
use CodeIgniter\Test\Fabricator;
use Faker\Generator;
use Tatter\Workflows\Entities\Job;

class JobModel extends Model
{
    protected $table          = 'jobs';
    protected $returnType     = Job::class;
    protected $useSoftDeletes = true;
    protected $useTimestamps  = true;
    protected $allowedFields  = [
        'name', 'summary', 'workflow_id', 'stage_id',
    ];
    protected $validationRules = [
        'name'        => 'required|max_length[255]',
        'summary'     => 'permit_empty|max_length[255]',
        'workflow_id' => 'required|is_natural_no_zero',
        'stage_id'    => 'permit_empty|is_natural_no_zero',
    ];
    protected $afterInsert  = ['logInsert'];
    protected $beforeUpdate = ['logUpdate'];

    /**
     * Logs successful insertions.
     *
     * @param array $data Event data from trigger
     *
     * @return array|false
     *
     * @psalm-return array{result: mixed}|false
     */
    protected function logInsert(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        // Add it to the database
        model(JoblogModel::class)->insert([
            'job_id'   => $data['id'],
            'stage_to' => $data['data']['stage_id'],
            'user_id'  => user_id(),
        ]);

        return $data;
    }

    /**
     * Logs updates that result in a stage change.
     *
     * @param array $data Event data from trigger
     */
    protected function logUpdate(array $data)
    {
        // Process each updated entry
        foreach ($data['id'] as $id) {
            // Get the job to be updated
            if (null === $job = $this->find($id)) {
                continue;
            }

            // Ignore when the stage will not be not touched
            if (! in_array('stage_id', array_keys($data['data']), true)) {
                continue;
            }

            // Ignore when the stage is the same
            if ((int) $data['data']['stage_id'] === (int) $job->stage_id) {
                continue;
            }

            // Add it to the database
            model(JoblogModel::class)->insert([
                'job_id'     => $job->id,
                'stage_from' => $job->stage_id,
                'stage_to'   => $data['data']['stage_id'],
                'user_id'    => user_id(),
            ]);
        }

        return $data;
    }

    /**
     * Faked data for Fabricator.
     */
    public function fake(Generator &$faker): Job
    {
        return new Job([
            'name'        => $faker->catchPhrase,
            'summary'     => $faker->sentence,
            'workflow_id' => random_int(1, Fabricator::getCount('workflows') ?: 4),
            'stage_id'    => random_int(1, Fabricator::getCount('stages') ?: 99),
        ]);
    }
}

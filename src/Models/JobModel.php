<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Workflows\Models;

use CodeIgniter\Model;
use CodeIgniter\Test\Fabricator;
use Faker\Generator;
use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Traits\JobsTrait;

class JobModel extends Model
{
    use JobsTrait;

    protected $table = 'jobs';

    protected $returnType = Job::class;

    protected $useSoftDeletes = true;

    protected $useTimestamps = true;

    protected $allowedFields = [
        'name', 'summary', 'workflow_id', 'stage_id',
    ];

    protected $validationRules = [
        'name'        => 'required|max_length[255]',
        'summary'     => 'permit_empty|max_length[255]',
        'workflow_id' => 'required|is_natural_no_zero',
        'stage_id'    => 'permit_empty|is_natural_no_zero',
    ];

    protected $afterInsert = ['logInsert'];

    protected $beforeUpdate = ['logUpdate'];

    /**
     * Faked data for Fabricator.
     */
    public function fake(Generator &$faker): Job
    {
        return new Job([
            'name'        => $faker->catchPhrase,
            'summary'     => $faker->sentence,
            'workflow_id' => mt_rand(1, Fabricator::getCount('workflows') ?: 4),
            'stage_id'    => mt_rand(1, Fabricator::getCount('stages') ?: 99),
        ]);
    }

    /**
     * Logs successful insertions.
     */
    protected function logInsert(array $eventData)
    {
        if (! $eventData['result']) {
            return false;
        }

        // Build the row
        $row = [
            'job_id'     => $eventData['id'],
            'stage_to'   => $eventData['data']['stage_id'] ?? null,
            'user_id'    => user_id(),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Add it to the database
        $this->builder('joblogs')->insert($row);

        return $eventData;
    }

    // Log updates that result in a stage change
    protected function logUpdate(array $data)
    {
        // Process each updated entry
        foreach ($data['id'] as $id) {
            // Get the job to be updated
            $job = $this->find($id);
            if (empty($job)) {
                continue;
            }

            // Ignore when the stage will not be not touched
            if (! in_array('stage_id', array_keys($data['data']), true)) {
                continue;
            }

            // Ignore when the stage is the same
            if ($data['data']['stage_id'] === $job->stage_id) {
                continue;
            }

            // Build the row
            $row = [
                'job_id'     => $job->id,
                'stage_from' => $job->stage_id,
                'stage_to'   => $data['data']['stage_id'],
                'user_id'    => user_id(),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // Add it to the database
            $this->builder('joblogs')->insert($row);
        }

        return $data;
    }
}

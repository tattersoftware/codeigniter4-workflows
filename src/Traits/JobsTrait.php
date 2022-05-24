<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Workflows\Traits;

/**
 * @mixin \Tatter\Workflows\Model\JobModel
 */
trait JobsTrait
{
    /**
     * Logs successful insertions.
     *
     * @param array $data Event data from trigger
     */
    protected function logInsert(array $data)
    {
        if (! $data['result']) {
            return false;
        }

        // Determine user source from config
        $userId = session(config('Workflows')->userSource) ?: 0;

        // Build the row
        $row = [
            'job_id'     => $data['id'],
            'stage_to'   => $data['data']['stage_id'],
            'user_id'    => $userId,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Add it to the database
        $this->builder('joblogs')->insert($row);

        return $data;
    }

    /**
     * Logs updates that result in a stage change.
     *
     * @param array $data Event data from trigger
     */
    protected function logUpdate(array $data)
    {
        db_connect();

        // Determine user source from config
        $userId = session(config('Workflows')->userSource);

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
                'user_id'    => $userId,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // Add it to the database
            $this->builder('joblogs')->insert($row);
        }

        return $data;
    }
}

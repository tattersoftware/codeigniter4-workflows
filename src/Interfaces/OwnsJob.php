<?php

namespace Tatter\Workflows\Interfaces;

use Tatter\Workflows\Entities\Job;

/**
 * Owns Job Interface
 *
 * Optional interface to define a User's
 * access to an individual Job.
 */
interface OwnsJob
{
    /**
     * Whether this user is considered an owner
     * of the given job. Affects general access
     * to modify and view the Job.
     */
    public function ownsJob(Job $job): bool;
}

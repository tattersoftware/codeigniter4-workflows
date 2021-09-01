<?php

namespace Tatter\Workflows\Test;

use CodeIgniter\Test\Fabricator;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;
use Tatter\Workflows\Registrar;

/**
 * Support class for simulating a complete workflow environment.
 */
class Simulator
{
    /**
     * Whether initialize() has been run.
     *
     * @var bool
     */
    public static $initialized = false;

    /**
     * Initialize the simulation.
     *
     * @param array $targets Array of target items to create
     */
    public static function initialize($targets = ['actions', 'jobs', 'stages', 'workflows'])
    {
        self::reset();

        // Register any Actions and update the count
        if (in_array('actions', $targets, true)) {
            Fabricator::setCount('actions', Registrar::actions());

            // Create actions up to N
            $count = mt_rand(10, 20);
            while (Fabricator::getCount('actions') < $count) {
                fake(ActionModel::class);
            }
        }

        // Create workflows up to N
        if (in_array('workflows', $targets, true)) {
            $count = mt_rand(2, 7);
            while (Fabricator::getCount('workflows') < $count) {
                fake(WorkflowModel::class);
            }
        }

        // Create stages up to N
        if (in_array('stages', $targets, true)) {
            $count = Fabricator::getCount('workflows') * mt_rand(4, 8);
            while (Fabricator::getCount('stages') < $count) {
                fake(StageModel::class);
            }
        }

        // Create jobs up to N
        if (in_array('jobs', $targets, true)) {
            $count = mt_rand(40, 200);
            while (Fabricator::getCount('jobs') < $count) {
                fake(JobModel::class);
            }
        }

        self::$initialized = true;
    }

    /**
     * Reset counts.
     */
    public static function reset()
    {
        // Reset counts on faked items
        Fabricator::resetCounts();

        self::$initialized = false;
    }
}

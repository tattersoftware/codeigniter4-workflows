<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Workflows\Test;

use CodeIgniter\Test\Fabricator;
use Tatter\Workflows\Factories\ActionFactory;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;

/**
 * Support class for simulating a complete workflow environment.
 */
class Simulator
{
    /**
     * Whether initialize() has been run.
     */
    public static bool $initialized = false;

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
            ActionFactory::register();

            $actions = model(ActionModel::class)->findAll();
            $count   = count($actions);
            Fabricator::setCount('actions', $count);

            // Create actions up to N
            $count = random_int(10, 20);

            while (Fabricator::getCount('actions') < $count) {
                fake(ActionModel::class);
            }
        }

        // Create workflows up to N
        if (in_array('workflows', $targets, true)) {
            $count = random_int(2, 7);

            while (Fabricator::getCount('workflows') < $count) {
                fake(WorkflowModel::class);
            }
        }

        // Create stages up to N
        if (in_array('stages', $targets, true)) {
            $count = Fabricator::getCount('workflows') * random_int(4, 8);

            while (Fabricator::getCount('stages') < $count) {
                fake(StageModel::class);
            }
        }

        // Create jobs up to N
        if (in_array('jobs', $targets, true)) {
            $count = random_int(40, 200);

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

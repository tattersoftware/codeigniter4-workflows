<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use CodeIgniter\Test\Fabricator;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;
use Tatter\Workflows\Test\Simulator;
use Tests\Support\DatabaseTestCase;

/**
 * @internal
 */
final class SimulatorTest extends DatabaseTestCase
{
    public function testFakeStageUsesCounts(): void
    {
        Fabricator::setCount('workflows', 10000);

        $sum = 0;

        for ($i = 0; $i < 3; $i++) {
            $stage = fake(StageModel::class);
            $sum += $stage->workflow_id;
        }

        $this->assertGreaterThan(12, $sum);
    }

    public function testInitializeCreatesMinimumObjects(): void
    {
        Simulator::initialize();

        $this->assertGreaterThanOrEqual(2, model(WorkflowModel::class)->countAllResults());
        $this->assertGreaterThanOrEqual(8, model(StageModel::class)->countAllResults());
        $this->assertGreaterThanOrEqual(40, model(JobModel::class)->countAllResults());
    }
}

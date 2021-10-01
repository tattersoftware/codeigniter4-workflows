<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;
use Tests\Support\DatabaseTestCase;

/**
 * @internal
 */
final class WorkflowModelTest extends DatabaseTestCase
{
    protected $migrateOnce = true;

    protected $seedOnce = true;

    public function testUpdateCreatesJoblog()
    {
        // Create a new Workflow with some Stages
        $workflow = fake(WorkflowModel::class);
        $stage1   = fake(StageModel::class, ['workflow_id' => $workflow->id]);
        $stage2   = fake(StageModel::class, ['workflow_id' => $workflow->id]);

        $expected = [$stage1, $stage2];

        $result = model(WorkflowModel::class)->fetchStages([$workflow]);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey($workflow->id, $result);

        $stages  = $result[$workflow->id];
        $result1 = reset($stages);
        $result2 = next($stages);

        $this->assertSame($stage1->id, $result1->id);
        $this->assertSame($stage2->id, $result2->id);
    }
}

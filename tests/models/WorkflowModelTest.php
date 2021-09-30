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
        $result   = model(WorkflowModel::class)->fetchStages([$workflow]);

        // This cannot be assertSame()
        $this->assertSame([$workflow->id => $expected], $result);
    }
}

<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;
use Tests\Support\DatabaseTestCase;

/**
 * @internal
 */
final class JobTest extends DatabaseTestCase
{
    protected $migrateOnce = true;
    protected $seedOnce    = true;

    public function testTravelCheck(): void
    {
        // Create the requirements
        $workflow = fake(WorkflowModel::class);
        $action1  = fake(ActionModel::class);
        $action2  = fake(ActionModel::class);

        // Create one required stage and one optional
        $stageRequired = fake(StageModel::class, [
            'action_id'   => $action1->id,
            'workflow_id' => $workflow->id,
            'required'    => 1,
        ]);
        $stageOptional = fake(StageModel::class, [
            'action_id'   => $action2->id,
            'workflow_id' => $workflow->id,
            'required'    => 0,
        ]);

        // Create a Job at the required Stage
        /** @var Job $job */
        $job = fake(JobModel::class, [
            'workflow_id' => $workflow->id,
            'stage_id'    => $stageRequired->id,
        ]);

        $this->expectException(WorkflowsException::class);
        $this->expectExceptionMessage('Cannot skip the required "' . $action1->name . '" action');

        $job->travel($stageOptional->action_id, true);
    }

    public function testTravelNotCheck(): void
    {
        // Create the requirements
        $workflow = fake(WorkflowModel::class);
        $action   = fake(ActionModel::class);

        // Create one required stage and one optional
        $stageRequired = fake(StageModel::class, [
            'action_id'   => $action->id,
            'workflow_id' => $workflow->id,
            'required'    => 1,
        ]);
        $stageOptional = fake(StageModel::class, [
            'action_id'   => $action->id,
            'workflow_id' => $workflow->id,
            'required'    => 0,
        ]);

        // Create a Job at the required Stage
        /** @var Job $job */
        $job = fake(JobModel::class, [
            'workflow_id' => $workflow->id,
            'stage_id'    => $stageRequired->id,
        ]);

        $job->travel($stageOptional->action_id, false);

        $result = model(JobModel::class)->find($job->id);
        $this->assertSame($stageRequired->id, $result->stage_id);
        $this->assertSame($stageRequired->id, $job->stage_id);
    }
}

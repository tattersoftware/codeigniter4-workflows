<?php

use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Entities\Stage;
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

    public function testGetStage(): void
    {
        // Create the requirements
        $workflow = fake(WorkflowModel::class);
        $stage    = fake(StageModel::class, [
            'action_id'   => 'info',
            'workflow_id' => $workflow->id,
        ]);

        /** @var Job $job */
        $job = fake(JobModel::class, [
            'workflow_id' => $workflow->id,
            'stage_id'    => $stage->id,
        ]);

        $result = $job->getStage();

        $this->assertInstanceOf(Stage::class, $result);
        $this->assertSame($stage->id, $result->id);
    }

    public function testGetStageThrowsOnMissing(): void
    {
        /** @var Job $job */
        $job = fake(JobModel::class, [
            'stage_id' => 42,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to locate Stage 42 for Job ' . $job->id);

        $job->getStage();
    }

    /* Temporarily disabled until another action is available.
     *
    public function testTravelNotCheck(): void
    {
        // Create the requirements
        $workflow = fake(WorkflowModel::class);

        // Create one required stage and one optional
        $stageRequired = fake(StageModel::class, [
            'action_id'   => 'info',
            'workflow_id' => $workflow->id,
            'required'    => 1,
        ]);
        $stageOptional = fake(StageModel::class, [
            'action_id'   => 'info',
            'workflow_id' => $workflow->id,
            'required'    => 0,
        ]);

        // Create a Job at the required Stage
        // @var Job $job
        $job = fake(JobModel::class, [
            'workflow_id' => $workflow->id,
            'stage_id'    => $stageRequired->id,
        ]);

        $job->travel($stageOptional->action_id, false);

        $result = model(JobModel::class)->find($job->id);
        $this->assertSame($stageRequired->id, $result->stage_id);
        $this->assertSame($stageRequired->id, $job->stage_id);
    }
    */
}

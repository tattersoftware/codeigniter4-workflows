<?php

use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;
use Tatter\Workflows\Test\Simulator;
use Tests\Support\DatabaseTestCase;

class JobTest extends DatabaseTestCase
{
	protected $migrateOnce = true;
	protected $seedOnce    = true;

	public function testTravelCheck()
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
		$job = fake(JobModel::class, [
			'workflow_id' => $workflow->id,
			'stage_id'    => $stageRequired->id,
		]);

		$this->expectException(WorkflowsException::class);
		$this->expectExceptionMessage('Cannot skip the required "' . $action1->name . '" action');

		$job->travel($stageOptional->action_id, true);
	}

	public function testTravelNotCheck()
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
		$job = fake(JobModel::class, [
			'workflow_id' => $workflow->id,
			'stage_id'    => $stageRequired->id,
		]);

		$job->travel($stageOptional->action_id, false);

		$result = model(JobModel::class)->find($job->id);
		$this->assertEquals($stageRequired->id, $result->stage_id);
	}
}

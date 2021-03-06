<?php

use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;
use Tests\Support\DatabaseTestCase;

class WorkflowModelTest extends DatabaseTestCase
{
	protected $migrateOnce = true;
	protected $seedOnce    = true;

	public function testUpdateCreatesJoblog()
	{
		// Create a new Workflow with some Stages
		$workflow = fake(WorkflowModel::class);
		$stage1   = fake(StageModel::class, ['workflow_id' => $workflow->id]);
		$stage2   = fake(StageModel::class, ['workflow_id' => $workflow->id]);

		$expected = [$stage1, $stage2];
		$result   = model(WorkflowModel::class)->fetchStages([$workflow]);

		$this->assertEquals([$workflow->id => $expected], $result);
	}
}

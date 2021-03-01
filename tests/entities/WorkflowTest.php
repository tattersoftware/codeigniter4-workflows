<?php

use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Entities\Workflow;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;
use Tests\Support\DatabaseTestCase;

class WorkflowTest extends DatabaseTestCase
{
	/**
	 * A random workflow to test with
	 *
	 * @var Workflow
	 */
	protected $workflow;

	protected function setUp(): void
	{
		parent::setUp();

		$this->workflow = fake(WorkflowModel::class);
	}

	public function testGetStages()
	{
		$stage = fake(StageModel::class, [
			'workflow_id' => $this->workflow->id,
		]);

		$result = $this->workflow->getStages();

		$this->assertIsArray($result);
		$this->assertCount(1, $result);

		$result = reset($result);
		$this->assertInstanceOf(Stage::class, $result);
		$this->assertEquals($stage->id, $result->id);
	}
}

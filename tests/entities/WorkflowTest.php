<?php

use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Entities\Workflow;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Test\Fakers\ActionFaker;
use Tatter\Workflows\Test\Fakers\StageFaker;
use Tatter\Workflows\Test\Fakers\WorkflowFaker;
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

		$this->workflow = fake(WorkflowFaker::class);
	}

	public function testGetStages()
	{
		$stage = fake(StageFaker::class, [
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

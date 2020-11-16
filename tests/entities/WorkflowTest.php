<?php

use Tatter\Workflows\Entities\Workflow;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Test\Fakers\ActionFaker;
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

	public function testAddActionCreatesStage()
	{
		$action = fake(ActionFaker::class);

		$result = $this->workflow->addAction($action);
		$this->assertIsInt($result);

		$stage = model(StageModel::class)->find($result);

		$this->assertEquals($action->id, $stage->action_id);
		$this->assertEquals($this->workflow->id, $stage->workflow_id);
	}

	public function testAddActionFailsWithUncreated()
	{
		$workflow = new Workflow();
		$action   = fake(ActionFaker::class);

		$this->expectException(\RuntimeException::class);

		$result = $workflow->addAction($action);
	}

	public function testAddActionUsesRequired()
	{
		$action = fake(ActionFaker::class);

		$id    = $this->workflow->addAction($action, true);
		$stage = model(StageModel::class)->find($id);

		$this->assertTrue($stage->required);
	}
}

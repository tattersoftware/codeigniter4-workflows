<?php

use CodeIgniter\Test\Fabricator;
use Tatter\Workflows\Entities\Workflow;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;
use Tatter\Workflows\Test\Simulator;
use Tests\Support\DatabaseTestCase;

class SimulatorTest extends DatabaseTestCase
{
	public function testFakeStageUsesCounts()
	{
		Fabricator::setCount('workflows', 10000);

		$sum = 0;

		for ($i = 0; $i < 3; $i++)
		{
			$stage = fake(StageModel::class);
			$sum  += $stage->workflow_id;
		}

		$this->assertGreaterThan(12, $sum);
	}

	public function testInitializeCreatesMinimumObjects()
	{
		Simulator::initialize();

		$this->assertGreaterThanOrEqual(10, model(ActionModel::class)->countAllResults());
		$this->assertGreaterThanOrEqual(2, model(WorkflowModel::class)->countAllResults());
		$this->assertGreaterThanOrEqual(8, model(StageModel::class)->countAllResults());
		$this->assertGreaterThanOrEqual(40, model(JobModel::class)->countAllResults());
	}

	public function testInitializeRegistersActions()
	{
		Simulator::initialize();

		$result = model(ActionModel::class)->first();

		$this->assertEquals('info', $result->uid);
	}
}

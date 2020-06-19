<?php

use Tatter\Workflows\Entities\Workflow;
use Tatter\Workflows\Test\Fakers\StageFaker;
use Tatter\Workflows\Test\Fakers\WorkflowFaker;
use Tatter\Workflows\Test\Simulator;
use Tests\Support\DatabaseTestCase;

class SimulatorTest extends DatabaseTestCase
{
	public function testFakeIncrementsCount()
	{
		$count = Simulator::$counts['workflows'];
		
		$workflow = fake(WorkflowFaker::class);

		$this->assertInstanceOf(Workflow::class, $workflow);
		$this->assertEquals($count + 1, Simulator::$counts['workflows']);
	}

	public function testFakeStageUsesCounts()
	{
		Simulator::$counts['workflows'] = 10000;
		
		$sum = 0;

		for ($i=0; $i<3; $i++)
		{
			$stage = fake(StageFaker::class);
			$sum += $stage->workflow_id;
		}

		$this->assertGreaterThan(12, $sum);
	}
}

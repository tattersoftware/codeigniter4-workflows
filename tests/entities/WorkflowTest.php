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
}

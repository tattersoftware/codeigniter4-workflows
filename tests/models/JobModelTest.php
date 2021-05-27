<?php

use Tatter\Workflows\Entities\Joblog;
use Tatter\Workflows\Models\JobModel;
use Tests\Support\DatabaseTestCase;

class JobModelTest extends DatabaseTestCase
{
	protected $migrateOnce = true;
	protected $seedOnce    = true;

	public function testInsertCreatesJoblog()
	{
		$jobId = model(JobModel::class)->insert([
			'name'        => 'Banana Job',
			'workflow_id' => 1,
			'stage_id'    => 42,
		]);

		$this->seeInDatabase('joblogs', [
			'job_id'     => $jobId,
			'stage_from' => null,
			'stage_to'   => 42,
		]);
	}

	public function testUpdateCreatesJoblog()
	{
		$job = fake(JobModel::class);

		model(JobModel::class)->update($job->id, [
			'stage_id' => $job->stage_id + 1,
		]);

		$this->seeInDatabase('joblogs', [
			'job_id'     => $job->id,
			'stage_from' => $job->stage_id,
			'stage_to'   => $job->stage_id + 1,
		]);
	}
}

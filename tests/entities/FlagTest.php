<?php namespace Tatter\Workflows\Entities;

use Tatter\Workflows\Test\Fakers\JobFaker;
use Tests\Support\DatabaseTestCase;

class FlagTest extends DatabaseTestCase
{
	/**
	 * A random Job to test with
	 *
	 * @var Job
	 */
	protected $job;

	protected function setUp(): void
	{
		parent::setUp();
		
		$this->job = fake(JobFaker::class);
	}

	public function testGetFlagsReturnsEmptyArray()
	{
		$result = $this->job->getFlags();

		$this->assertEquals($result, []);
	}

	public function testGetFlagsReturnsFlagsArray()
	{
		$now = date('Y-m-d H:i:s');

		$this->db->table('jobflags')->insert([
			'job_id'     => $this->job->id,
			'name'       => 'foobar',
			'created_at' => $now,
		]);

		$result = $this->job->getFlags();

		$this->assertCount(1, $result);
		$this->assertEquals($now, $result['foobar']);
	}
}

<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\I18n\Time;
use Tatter\Workflows\Test\Fakers\JobFaker;
use Tests\Support\DatabaseTestCase;

class JobflagTest extends DatabaseTestCase
{
	/**
	 * Common timestamp to use during testing
	 *
	 * @var string
	 */
	protected $now;

	/**
	 * A random Job to test with
	 *
	 * @var Job
	 */
	protected $job;

	/**
	 * Builder for `jobflags`
	 *
	 * @var BaseBuilder
	 */
	protected $builder;

	protected function setUp(): void
	{
		parent::setUp();

		$this->now     = date('Y-m-d H:i:s');
		$this->job     = fake(JobFaker::class);
		$this->builder = $this->db->table('jobflags');
	}

	/**
	 * Create a flag with $name on $this->job
	 *
	 * @param string $name
	 *
	 * @return array Result from the database
	 */
	protected function createFlag(string $name = 'foobar'): array
	{
		$this->builder->insert([
			'job_id'     => $this->job->id,
			'name'       => 'foobar',
			'created_at' => $this->now,
		]);

		return $this->builder->getWhere(['id' => $this->db->insertID()])->getRowArray();
	}

	//--------------------------------------------------------------------

	public function testGetFlagsReturnsEmptyArray()
	{
		$result = $this->job->getFlags();

		$this->assertEquals($result, []);
	}

	public function testGetFlagsReturnsFlagsArray()
	{
		$flag = $this->createFlag();

		$result = $this->job->getFlags();

		$this->assertCount(1, $result);
		$this->assertEquals($this->now, $result['foobar']);
	}

	public function testGetFlagReturnsNull()
	{
		$result = $this->job->getFlag('foobar');

		$this->assertNull($result);
	}

	public function testGetFlagReturnsTime()
	{
		$flag = $this->createFlag();

		$result = $this->job->getFlag('foobar');

		$this->assertInstanceOf(Time::class, $result);
		$this->assertEquals($this->now, $result->toDateTimeString());
	}

	public function testGetFlagStoresValues()
	{
		$flag = $this->createFlag();
		$this->job->getFlags();
		$this->builder->truncate();

		$result = $this->job->getFlag('foobar');

		$this->assertInstanceOf(Time::class, $result);
	}

	public function testSetFlagCreates()
	{
		$this->job->setFlag('barbam');

		$this->seeInDatabase('jobflags', ['job_id' => $this->job->id]);
	}

	public function testClearFlagDeletes()
	{
		$flag = $this->createFlag();
		$this->job->clearFlag('foobar');

		$result = $this->job->getFlag('foobar');

		$this->assertNull($result);
	}

	public function testClearFlagsDeletesAll()
	{
		$this->createFlag();
		$this->createFlag('barbam');
		$this->createFlag('bambaz');

		$this->job->clearFlags();

		$result = $this->job->getFlags();

		$this->assertEquals([], $result);
		$this->dontSeeInDatabase('jobflags', ['job_id' => $this->job->id]);
	}
}

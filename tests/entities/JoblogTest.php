<?php

use Myth\Auth\Test\Fakers\UserFaker;
use Tatter\Users\UserEntity;
use Tatter\Workflows\Entities\Joblog;
use Tests\Support\DatabaseTestCase;

class JoblogTest extends DatabaseTestCase
{
	/**
	 * A Joblog to test with
	 *
	 * @var Joblog
	 */
	private $joblog;

	protected function setUp(): void
	{
		parent::setUp();

		$this->joblog = new Joblog([
			'job_id'     => 1,
			'stage_from' => 1,
			'stage_to'   => 2,
			'user_id'    => null,
		]);
	}

	public function testGetUser()
	{
		$user = fake(UserFaker::class);

		$this->joblog->user_id = $user->id;

		$result = $this->joblog->getUser();

		$this->assertInstanceOf(UserEntity::class, $result);
		$this->assertEquals($user->id, $result->getId());
	}
}

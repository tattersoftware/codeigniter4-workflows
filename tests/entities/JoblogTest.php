<?php

use Myth\Auth\Entities\User;
use Myth\Auth\Test\Fakers\UserFaker;
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

	/**
	 * Tests the Entity's ability to locate
	 * a valid UserModel and return a User
	 * matched to its user_id attrbiute
	 */
	public function testCanLocateUser()
	{
		$user = fake(UserFaker::class);

		$this->joblog->user_id = $user->id;

		$result = $this->joblog->getUser();

		$this->assertInstanceOf(User::class, $result);
		$this->assertEquals($user->id, $result->id);
	}
}

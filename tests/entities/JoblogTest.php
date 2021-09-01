<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Myth\Auth\Test\Fakers\UserFaker;
use Tatter\Users\UserEntity;
use Tatter\Workflows\Entities\Joblog;
use Tests\Support\DatabaseTestCase;

/**
 * @internal
 */
final class JoblogTest extends DatabaseTestCase
{
    /**
     * A Joblog to test with.
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
        $this->assertSame($user->id, $result->getId());
    }
}

<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Workflows\Entities;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\I18n\Time;
use Tatter\Workflows\Models\JobModel;
use Tests\Support\DatabaseTestCase;

/**
 * @internal
 */
final class JobflagTest extends DatabaseTestCase
{
    /**
     * Common timestamp to use during testing.
     */
    protected string $now;

    /**
     * A random Job to test with.
     */
    protected Job $job;

    /**
     * Builder for `jobflags`.
     */
    protected BaseBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->now     = date('Y-m-d H:i:s');
        $this->job     = fake(JobModel::class);
        $this->builder = $this->db->table('jobflags');
    }

    //--------------------------------------------------------------------

    public function testGetFlagsReturnsEmptyArray(): void
    {
        $result = $this->job->getFlags();

        $this->assertSame($result, []);
    }

    public function testGetFlagsReturnsFlagsArray(): void
    {
        $this->createFlag();

        $result = $this->job->getFlags();

        $this->assertCount(1, $result);
        $this->assertSame($this->now, $result['foobar']->toDateTimeString());
    }

    public function testGetFlagReturnsNull(): void
    {
        $result = $this->job->getFlag('foobar');

        $this->assertNull($result);
    }

    public function testGetFlagReturnsTime(): void
    {
        $this->createFlag();

        $result = $this->job->getFlag('foobar');

        $this->assertInstanceOf(Time::class, $result);
        $this->assertSame($this->now, $result->toDateTimeString());
    }

    public function testGetFlagStoresValues(): void
    {
        $this->createFlag();
        $this->job->getFlags();
        $this->builder->truncate();

        $result = $this->job->getFlag('foobar');

        $this->assertInstanceOf(Time::class, $result);
    }

    public function testSetFlagCreates(): void
    {
        $this->job->setFlag('barbam');

        $this->seeInDatabase('jobflags', ['job_id' => $this->job->id]);
    }

    public function testClearFlagDeletes(): void
    {
        $this->createFlag();
        $this->job->clearFlag('foobar');

        $result = $this->job->getFlag('foobar');

        $this->assertNull($result);
    }

    public function testClearFlagsDeletesAll(): void
    {
        $this->createFlag();
        $this->createFlag('barbam');
        $this->createFlag('bambaz');

        $this->job->clearFlags();

        $result = $this->job->getFlags();

        $this->assertSame([], $result);
        $this->dontSeeInDatabase('jobflags', ['job_id' => $this->job->id]);
    }

    /**
     * Create a flag with $name on $this->job.
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
}

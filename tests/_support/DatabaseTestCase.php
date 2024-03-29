<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tatter\Imposter\Entities\User;
use Tatter\Imposter\Factories\ImposterFactory;
use Tatter\Users\UserProvider;
use Tatter\Workflows\Config\Workflows as WorkflowsConfig;
use Tatter\Workflows\Test\Simulator;

abstract class DatabaseTestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;

    /**
     * Should the database be refreshed before each test?
     *
     * @var bool
     */
    protected $refresh = true;

    /**
     * The namespace(s) to help us find the migration classes.
     * Empty is equivalent to running `spark migrate -all`.
     * Note that running "all" runs migrations in date order,
     * but specifying namespaces runs them in namespace order (then date).
     *
     * @var array|string|null
     */
    protected $namespace;

    /**
     * Preconfigured config instance.
     */
    protected WorkflowsConfig $config;

    /**
     * Loads the auth helper.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        UserProvider::addFactory(ImposterFactory::class, ImposterFactory::class);

        helper('auth');
    }

    /**
     * Makes sure all errors throw exceptions.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->config = config('Workflows');
    }

    /**
     * Resets the Simulator.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        Simulator::reset();
        ImposterFactory::reset();
    }

    protected function fakeUser(): User
    {
        $user     = ImposterFactory::fake();
        $user->id = ImposterFactory::add($user);

        return $user;
    }
}

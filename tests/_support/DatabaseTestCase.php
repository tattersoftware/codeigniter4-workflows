<?php namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tatter\Workflows\Config\Workflows as WorkflowsConfig;
use Tatter\Workflows\Test\Simulator;

class DatabaseTestCase extends CIUnitTestCase
{
	use DatabaseTestTrait;

	/**
	 * Should the database be refreshed before each test?
	 *
	 * @var boolean
	 */
	protected $refresh = true;

	/**
	 * The namespace(s) to help us find the migration classes.
	 * Empty is equivalent to running `spark migrate -all`.
	 * Note that running "all" runs migrations in date order,
	 * but specifying namespaces runs them in namespace order (then date)
	 *
	 * @var string|array|null
	 */
	protected $namespace = null;

	/**
	 * Preconfigured config instance.
	 */
	protected $config;

	/**
	 * Loads the auth helper
	 */
	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		helper('auth');
	}

	/**
	 * Makes sure all errors throw exceptions
	 */
	protected function setUp(): void
	{
		parent::setUp();

		$this->config         = new WorkflowsConfig();
		$this->config->silent = false;
	}

	/**
	 * Resets the Simulator
	 */
	protected function tearDown(): void
	{
		parent::tearDown();

		Simulator::reset();
	}
}

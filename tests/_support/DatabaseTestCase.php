<?php namespace Tests\Support;

use Config\Services;
use CodeIgniter\Session\Handlers\ArrayHandler;
use CodeIgniter\Test\CIDatabaseTestCase;
use CodeIgniter\Test\Mock\MockSession;
use Tatter\Workflows\Test\Simulator;

class DatabaseTestCase extends CIDatabaseTestCase
{
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
    protected $namespace = 'Tatter\Workflows';

	/**
	 * Preconfigured config instance.
	 */
	protected $config;

    /**
     * Initializes the one-time components.
     */
    public static function setUpBeforeClass(): void
    {
    	helper('test');

		// Inject the mock session driver into Services
        $config  = config('App');
        $session = new MockSession(new ArrayHandler($config, '0.0.0.0'), $config);
        Services::injectMock('session', $session);
    }

	protected function setUp(): void
	{
		parent::setUp();
		
		$this->config         = new \Tatter\Workflows\Config\Workflows();
		$this->config->silent = false;
	}
	
	protected function tearDown(): void
	{
		parent::tearDown();

		Simulator::reset();
    	$_SESSION = [];
	}
}

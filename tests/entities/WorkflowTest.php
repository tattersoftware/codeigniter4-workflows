<?php

use Myth\Auth\Test\Fakers\UserFaker;
use Tatter\Users\Factories\MythFactory;
use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Entities\Workflow;
use Tatter\Workflows\Models\ExplicitModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;
use Tests\Support\DatabaseTestCase;

/**
 * @internal
 */
final class WorkflowTest extends DatabaseTestCase
{
	/**
	 * A fake Workflow to test with.
	 *
	 * @var Workflow
	 */
	private $workflow;

	protected function setUp(): void
	{
		parent::setUp();

		$this->workflow = fake(WorkflowModel::class);
	}

	public function testGetStages()
	{
		$stage = fake(StageModel::class, [
			'workflow_id' => $this->workflow->id,
		]);

		$result = $this->workflow->getStages();

		$this->assertIsArray($result);
		$this->assertCount(1, $result);

		$result = reset($result);
		$this->assertInstanceOf(Stage::class, $result);
		$this->assertSame($stage->id, $result->id);
	}

	public function testMayAccessEmpty()
	{
		$this->workflow->role = '';

		$this->assertTrue($this->workflow->mayAccess());
	}

	public function testMayAccessExplicit()
	{
		$explicit              = $this->createExplicit();
		$_SESSION['logged_in'] = $explicit->user_id;

		$this->workflow->role = 'restricted';

		$this->assertTrue($this->workflow->mayAccess());
	}

	public function testMayAccessExplicitWithUser()
	{
		$explicit = $this->createExplicit();

		// Get the UserEntity with HasPermission
		$user = (new MythFactory())->findById($explicit->user_id);

		$this->workflow->role = 'restricted';

		$this->assertTrue($this->workflow->mayAccess($user));
	}

	public function testMayAccessExplicitWithExplicits()
	{
		$explicit = $this->createExplicit();

		$this->workflow->role = 'restricted';

		$this->assertTrue($this->workflow->mayAccess(null, [$explicit->id => $explicit->permitted]));
	}

	public function testMayAccessExplicitWithBoth()
	{
		$explicit = $this->createExplicit();

		// Get the UserEntity with HasPermission
		$user = (new MythFactory())->findById($explicit->user_id);

		$this->workflow->role = 'restricted';

		$this->assertTrue($this->workflow->mayAccess($user, [$explicit->id => $explicit->permitted]));
	}

	public function testMayNotAccess()
	{
		$this->workflow->role = 'restricted';

		$this->assertFalse($this->workflow->mayAccess());
	}

	public function testMayNotAccessExplicit()
	{
		$explicit              = $this->createExplicit(['permitted' => 0]);
		$_SESSION['logged_in'] = $explicit->user_id;

		$this->workflow->role = '';

		$this->assertFalse($this->workflow->mayAccess());
	}

	public function testMayNotAccessExplicitWithUser()
	{
		$explicit = $this->createExplicit(['permitted' => 0]);

		// Get the UserEntity with HasPermission
		$user = (new MythFactory())->findById($explicit->user_id);

		$this->workflow->role = '';

		$this->assertFalse($this->workflow->mayAccess($user));
	}

	public function testMayNotAccessExplicitWithExplicits()
	{
		$explicit = $this->createExplicit(['permitted' => 0]);

		$this->workflow->role = '';

		$this->assertFalse($this->workflow->mayAccess(null, [$explicit->id => $explicit->permitted]));
	}

	public function testMayNotAccessExplicitWithBoth()
	{
		$explicit = $this->createExplicit(['permitted' => 0]);

		// Get the UserEntity with HasPermission
		$user = (new MythFactory())->findById($explicit->user_id);

		$this->workflow->role = '';

		$this->assertFalse($this->workflow->mayAccess($user, [$explicit->id => $explicit->permitted]));
	}

	/**
	 * Create a fake explicit to test with.
	 *
	 * @param array $data
	 *
	 * @return object
	 */
	private function createExplicit(array $data = []): object
	{
		$user = fake(UserFaker::class);

		$data = array_merge([
			'user_id'     => $user->id,
			'workflow_id' => $this->workflow->id,
			'permitted'   => 1,
		], $data);

		return fake(ExplicitModel::class, $data);
	}
}

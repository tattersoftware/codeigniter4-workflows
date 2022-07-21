<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Tatter\Imposter\Factories\ImposterFactory;
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
    protected $refresh = true;

    /**
     * A fake Workflow to test with.
     */
    private Workflow $workflow;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workflow = fake(WorkflowModel::class);
    }

    public function testEnsureCreated(): void
    {
        $workflow = new Workflow();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(Workflow::class . ' must exist in the database.');

        $workflow->getStages();
    }

    public function testGetStages(): void
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

    public function testGetStageById(): void
    {
        $stage = fake(StageModel::class, [
            'workflow_id' => $this->workflow->id,
        ]);

        $result = $this->workflow->getStageById($stage->id);

        $this->assertInstanceOf(Stage::class, $result);
        $this->assertSame($stage->id, $result->id);
    }

    public function testGetStageByIdFails(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Workflow ' . $this->workflow->id . ' does not contain stage 42');

        $this->workflow->getStageById(42);
    }

    public function testMayAccessEmpty(): void
    {
        $this->workflow->role = '';

        $this->assertTrue($this->workflow->mayAccess());
    }

    public function testMayAccessExplicit(): void
    {
        $explicit              = $this->createExplicit();
        $_SESSION['logged_in'] = $explicit->user_id;

        $this->workflow->role = 'restricted';

        $this->assertTrue($this->workflow->mayAccess());
    }

    public function testMayAccessExplicitWithUser(): void
    {
        $explicit = $this->createExplicit();

        // Get the UserEntity with HasPermission
        $user = (new ImposterFactory())->findById($explicit->user_id);

        $this->workflow->role = 'restricted';

        $this->assertTrue($this->workflow->mayAccess($user));
    }

    public function testMayAccessExplicitWithExplicits(): void
    {
        $explicit = $this->createExplicit();

        $this->workflow->role = 'restricted';

        $this->assertTrue($this->workflow->mayAccess(null, [$explicit->id => $explicit->permitted]));
    }

    public function testMayAccessExplicitWithBoth(): void
    {
        $explicit = $this->createExplicit();

        // Get the UserEntity with HasPermission
        $user = (new ImposterFactory())->findById($explicit->user_id);

        $this->workflow->role = 'restricted';

        $this->assertTrue($this->workflow->mayAccess($user, [$explicit->id => $explicit->permitted]));
    }

    public function testMayNotAccess(): void
    {
        $this->workflow->role = 'restricted';

        $this->assertFalse($this->workflow->mayAccess());
    }

    public function testMayNotAccessExplicit(): void
    {
        $explicit              = $this->createExplicit(['permitted' => 0]);
        $_SESSION['logged_in'] = $explicit->user_id;

        $this->workflow->role = '';

        $this->assertFalse($this->workflow->mayAccess());
    }

    public function testMayNotAccessExplicitWithUser(): void
    {
        $explicit = $this->createExplicit(['permitted' => 0]);

        // Get the UserEntity with HasPermission
        $user = (new ImposterFactory())->findById($explicit->user_id);

        $this->workflow->role = '';

        $this->assertFalse($this->workflow->mayAccess($user));
    }

    public function testMayNotAccessExplicitWithExplicits(): void
    {
        $explicit = $this->createExplicit(['permitted' => 0]);

        $this->workflow->role = '';

        $this->assertFalse($this->workflow->mayAccess(null, [$explicit->id => $explicit->permitted]));
    }

    public function testMayNotAccessExplicitWithBoth(): void
    {
        $explicit = $this->createExplicit(['permitted' => 0]);

        // Get the UserEntity with HasPermission
        $user = (new ImposterFactory())->findById($explicit->user_id);

        $this->workflow->role = '';

        $this->assertFalse($this->workflow->mayAccess($user, [$explicit->id => $explicit->permitted]));
    }

    /**
     * Create a fake explicit to test with.
     *
     * @return array|object
     */
    private function createExplicit(array $data = [])
    {
        $user = $this->fakeUser();

        $data = array_merge([
            'user_id'     => $user->id,
            'workflow_id' => $this->workflow->id,
            'permitted'   => 1,
        ], $data);

        return fake(ExplicitModel::class, $data);
    }
}

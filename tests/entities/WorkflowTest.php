<?php

use Tatter\Imposter\Entities\User;
use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Entities\Workflow;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Models\ExplicitModel;
use Tatter\Workflows\Models\JobModel;
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

    /**
     * Create a fake explicit to test with.
     *
     * @return array|object
     */
    private function createExplicit(User $user, array $data = [])
    {
        $data = array_merge([
            'user_id'     => $user->id,
            'workflow_id' => $this->workflow->id,
            'permitted'   => 1,
        ], $data);

        return fake(ExplicitModel::class, $data);
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

    public function testGetStageByAction(): void
    {
        $stage = fake(StageModel::class, [
            'workflow_id' => $this->workflow->id,
            'action_id'   => 'banana',
        ]);

        $result = $this->workflow->getStageByAction('banana');

        $this->assertInstanceOf(Stage::class, $result);
        $this->assertSame($stage->id, $result->id);
    }

    public function testGetStageByActionFails(): void
    {
        $this->expectException(WorkflowsException::class);
        $this->expectExceptionMessage($this->workflow->name . ' does not contain action falafel');

        $this->workflow->getStageByAction('falafel');
    }

    public function testAllowsUserEmpty(): void
    {
        $this->workflow->role = '';

        $this->assertTrue($this->workflow->allowsUser(null));
    }

    public function testAllowsUserExplicit(): void
    {
        $user = $this->fakeUser();
        $this->createExplicit($user);

        $this->workflow->role = 'restricted';

        $this->assertTrue($this->workflow->allowsUser($user));
    }

    public function testAllowsUserExplicitWithExplicits(): void
    {
        $user     = $this->fakeUser();
        $explicit = $this->createExplicit($user);

        $this->workflow->role = 'restricted';

        $this->assertTrue($this->workflow->allowsUser($user, [$explicit->workflow_id => $explicit->permitted]));
    }

    public function testNotAllowsUser(): void
    {
        $this->workflow->role = 'restricted';

        $this->assertFalse($this->workflow->allowsUser(null));
    }

    public function testNotAllowsUserExplicit(): void
    {
        $user = $this->fakeUser();
        $this->createExplicit($user, ['permitted' => 0]);

        $this->workflow->role = '';

        $this->assertFalse($this->workflow->allowsUser($user));
    }

    public function testNotAllowsUserExplicitWithExplicits(): void
    {
        $user     = $this->fakeUser();
        $explicit = $this->createExplicit($user, ['permitted' => 0]);

        $this->workflow->role = '';

        $this->assertFalse($this->workflow->allowsUser($user, [$explicit->workflow_id => $explicit->permitted]));
    }

    public function testProgress(): void
    {
        // Create the requirements
        /** @var Workflow $workflow */
        $workflow = fake(WorkflowModel::class);
        $stage1   = fake(StageModel::class, [
            'action_id'   => 'info',
            'workflow_id' => $workflow->id,
            'required'    => 1,
        ]);
        $stage2 = fake(StageModel::class, [
            'action_id'   => 'info',
            'workflow_id' => $workflow->id,
            'required'    => 0,
        ]);

        $job = fake(JobModel::class, [
            'workflow_id' => $workflow->id,
            'stage_id'    => $stage1->id,
        ]);

        $workflow->progress($job);

        $this->assertSame($stage2->id, $job->stage_id);
    }
}

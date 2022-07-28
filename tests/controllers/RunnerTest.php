<?php

use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;
use Tatter\Workflows\Controllers\Runner;
use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;
use Tests\Support\DatabaseTestCase;

/**
 * @internal
 */
final class RunnerTest extends DatabaseTestCase
{
    use ControllerTestTrait;
    use DatabaseTestTrait;

    protected $migrateOnce = true;
    protected $seedOnce    = true;

    /**
     * Sets up the Controller for testing.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->controller(Runner::class);
    }

    /**
     * Create a fake Job to test with.
     *
     * @return Job
     */
    private function createJob(array $data = [])
    {
        $workflow = fake(WorkflowModel::class);
        $stage    = fake(StageModel::class, [
            'action_id'   => 'info',
            'workflow_id' => $workflow->id,
            'required'    => 1,
        ]);
        fake(StageModel::class, [
            'action_id'   => 'button',
            'workflow_id' => $workflow->id,
            'required'    => 0,
        ]);

        $data = array_merge([
            'workflow_id' => $workflow->id,
            'stage_id'    => $stage->id,
        ], $data);

        return fake(JobModel::class, $data);
    }

    public function testResumeMissingParam()
    {
        $result = $this->execute('resume');

        $result->assertStatus(200);
        $result->assertSee(lang('Workflows.jobNotFound'));
    }

    public function testRunMissingParams()
    {
        $result = $this->execute('run');

        $result->assertStatus(404);
    }

    public function testRunMissingAction()
    {
        $result = $this->execute('run', 'banana');

        $result->assertStatus(200);
        $result->assertSee(lang('Workflows.actionNotFound'));
    }

    public function testRunMissingJob()
    {
        $result = $this->execute('run', 'info');

        $result->assertStatus(200);
        $result->assertSee(lang('Workflows.routeMissingJobId', ['info']));
    }

    public function testRunJobNotFound()
    {
        $result = $this->execute('run', 'info', '42');

        $result->assertStatus(200);
        $result->assertSee(lang('Workflows.jobNotFound'));
    }

    public function testApplyActionDeletedJob()
    {
        $job = $this->createJob();
        model(JobModel::class)->delete($job->id);

        $result = $this->execute('run', 'info', $job->id);

        $result->assertStatus(200);
        $result->assertSee(lang('Workflows.useDeletedJob'));
    }

    public function testApplyActionCompletedJob()
    {
        $job = $this->createJob(['stage_id' => null]);

        $result = $this->execute('run', 'info', $job->id);

        $result->assertRedirect();
        $result->assertRedirectTo($job->getUrl());
        $result->assertSessionHas('message', lang('Workflows.jobAlreadyComplete'));
    }

    public function testApplyActionTravelFailsRequired()
    {
        $job = $this->createJob();

        $result = $this->execute('run', 'button', $job->id);

        $result->assertStatus(200);
        $result->assertSee(lang('Workflows.skipRequiredStage', ['Info']));
    }

    public function testApplyActionMissingMethod()
    {
        $job = $this->createJob();
        $this->request->setMethod('patch'); // @phpstan-ignore-line

        $result = $this->execute('run', 'info', $job->id);

        $result->assertStatus(404);
    }

    public function testHandleResponseView()
    {
        $job = $this->createJob();

        $result = $this->execute('run', 'info', $job->id);

        $result->assertStatus(200);
        $result->assertSee('Basic info', 'h4');
    }

    public function testHandleResponseValidation()
    {
        $job = $this->createJob();
        $this->request->setMethod('post'); // @phpstan-ignore-line

        $result = $this->execute('run', 'info', $job->id);

        $result->assertRedirect();
        $result->assertSessionHas('errors', ['name' => 'The name field is required.']);
    }

    /*
        // Failing because for some reason execute isn't passing in the POST value
        public function testHandleResponseNull()
        {
            $job = $this->createJob();
            $this->request->setMethod('post');
            $this->request->setGlobal('post', ['name' => 'Banana']);

            $result = $this->execute('run', 'info', $job->id);

            $result->assertRedirect();
            $result->assertRedirectTo($job->getUrl());
        }
    */
}

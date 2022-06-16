<?php

namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Workflows\Config\Workflows as WorkflowsConfig;
use Tatter\Workflows\Entities\Action;
use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;

/**
 * Class Runner.
 *
 * Functions as a super-controller, routing jobs to their specific actions
 * and action functions with included metadata.
 */
class Runner extends Controller
{
    use ErrorTrait;

    /**
     * @var WorkflowsConfig
     */
    protected $config;

    /**
     * @var JobModel Module version or extension thereof
     */
    protected $jobs;

    /**
     * Preload the config class and Model for jobs.
     */
    public function __construct()
    {
        $this->config = config('Workflows');
        $this->jobs   = model($this->config->jobModel); // @phpstan-ignore-line
    }

    /**
     * Resume a Job at its current Stage.
     *
     * @param int|string $jobId ID of the job to resume
     *
     * @return RedirectResponse|ResponseInterface A view to display or a RedirectResponse
     */
    public function resume($jobId): ResponseInterface
    {
        // Get the Job
        if ($jobId) {
            /** @var Job $job */
            $job = $this->jobs->find($jobId);
        }

        if (empty($job)) {
            return $this->handleError(WorkflowsException::forJobNotFound());
        }

        // If the Job is completed then display a message and quit
        if (empty($job->stage_id)) {
            $this->response->setBody(view($this->config->views['messages'], [
                'layout'  => config('Layouts')->public,
                'message' => lang('Workflows.jobAlreadyComplete'),
            ]));

            return $this->response;
        }

        // Check for a current Stage
        if (! $stage = model(StageModel::class)->find($job->stage_id)) {
            return $this->handleError(new WorkflowsException(lang('Workflows.stageNotFound')));
        }

        return redirect()->to($stage->action->getRoute($job->id));
    }

    /**
     * Receives route input and handles action coordination.
     *
     * @param string ...$params Parameters coming from the router (so all strings)
     *
     * @throws PageNotFoundException
     */
    public function run(string ...$params): ResponseInterface
    {
        if (empty($params)) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Parse the route parameters
        try {
            // Extract parsed variables
            [$action, $job, $stage] = $this->parseRoute($params);
        } catch (WorkflowsException $e) {
            return $this->handleError($e);
        }

        // Intercept Jobs that are already completed
        if (empty($stage)) {
            return redirect()->to(site_url($this->config->routeBase . '/show/' . $job->id));
        }

        // If the requested Action differs from the Job's current Action then travel the Workflow
        if ($action->id !== $stage->action_id) {
            try {
                $job->travel($action->id);
            } catch (WorkflowsException $e) {
                return $this->handleError($e);
            }
        }

        // Check the Action's role against a potential current user
        if (! $action->mayAccess()) {
            $this->response->setBody(view($this->config->views['filter'], [
                'layout' => config('Layouts')->public,
                'job'    => $job,
            ]));

            return $this->response;
        }

        // Determine the request method and run the corresponding Action method
        $method = $this->request->getMethod(); // @phpstan-ignore-line

        try {
            $result = $action->setJob($job)->{$method}();
        } catch (WorkflowsException $e) {
            $this->response->setBody(view($this->config->views['messages'], [
                'layout' => config('Layouts')->public,
                'error'  => $e->getMessage(),
            ]));

            return $this->response;
        }

        // If it was a Response then we are done
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        // Null means the Stage is complete
        return $this->progress($job);
    }

    /**
     * Validates and parses values from a route.
     *
     * @param array $params Parameters coming from the router (so all strings)
     *
     * @throws WorkflowsException
     *
     * @return array Array of parsed data
     */
    protected function parseRoute(array $params)
    {
        // Strip off the Action & Job identifiers
        $uid   = array_shift($params);
        $jobId = array_shift($params);

        // Verify the ID
        if (empty($jobId) || ! is_numeric($jobId)) {
            throw WorkflowsException::forMissingJobId($uid);
        }

        // Look up the Action by its UID
        if (! $action = model(ActionModel::class)->where('uid', $uid)->first()) {
            throw WorkflowsException::forActionNotFound();
        }

        // Load the Job
        if (! $job = $this->jobs->find($jobId)) {
            throw WorkflowsException::forJobNotFound();
        }

        // Verify the Workflow
        if (! $workflow = model(WorkflowModel::class)->find($job->workflow_id)) {
            throw WorkflowsException::forWorkflowNotFound();
        }

        // stage_id may be empty (completed Job)
        $stage = $job->stage_id ? model(StageModel::class)->find($job->stage_id) : null;

        return [
            $action,
            $job,
            $stage,
        ];
    }

    /**
     * Progresses a Job after an Action indicates
     * that the current Stage is done.
     */
    protected function progress(Job $job): ResponseInterface
    {
        // Get the next Stage
        if ($stage = $job->next()) {
            // Travel to the next Action
            $job->travel($stage->action_id, false);

            return redirect()->to($stage->action->getRoute($job->id));
        }

        // Update the Job as complete
        $this->jobs->update($job->id, ['stage_id' => null]);

        $this->response->setBody(view($this->config->views['complete'], [
            'layout' => config('Layouts')->public,
            'job'    => $job,
        ]));

        return $this->response;
    }
}

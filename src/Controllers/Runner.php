<?php

namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;
use Tatter\Users\Interfaces\HasPermission;
use Tatter\Workflows\Entities\Action;
use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Factories\ActionFactory;
use Tatter\Workflows\Models\StageModel;

/**
 * Class Runner.
 *
 * Functions as a super-controller, routing to their specific
 * Action controller and method with job data.
 */
final class Runner extends BaseController
{
    /**
     * Resume a Job at its current Stage.
     *
     * @param int|string $jobId ID of the job to resume
     *
     * @return RedirectResponse|ResponseInterface A view to display or a RedirectResponse
     */
    public function resume($jobId = ''): ResponseInterface
    {
        // Get the Job
        if ($jobId) {
            $this->job = $this->jobs->find($jobId);
        }

        if ($this->job === null) {
            return $this->renderError(lang('Workflows.jobNotFound'));
        }

        // If the Job is completed then display a message and quit
        if ($this->job->stage_id === null) {
            return $this->renderMessage(lang('Workflows.jobAlreadyComplete'));
        }

        // Get the current Stage
        if (! $stage = model(StageModel::class)->find($this->job->stage_id)) {
            throw new RuntimeException('Unknown stage ID: ' . $this->job->stage_id);
        }

        /** @var Stage $stage */
        return redirect()->to($stage->getRoute() . $this->job->id);
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
            return $this->renderError($e->getMessage());
        }
        $this->setJob($job);

        // Intercept Jobs that are already completed
        if (empty($stage)) {
            return redirect()->to(site_url($this->config->routeBase . '/show/' . $job->id));
        }

        // If the requested Action differs from the Job's current Action then travel the Workflow
        if ($action::HANDLER_ID !== $stage->action_id) {
            try {
                $job->travel($action->id);
            } catch (WorkflowsException $e) {
                return $this->renderError($e->getMessage());
            }
        }

        // Check the Action's role against a potential current user
        if (! $this->checkAccess($action)) {
            return $this->renderMessage(lang('Workflows.jobAwaitingInput', $this->job->name));
        }

        // Determine the request method and verify the corresponding Action method exists
        $method = $this->request->getMethod(); // @phpstan-ignore-line
        if (! method_exists($action, $method)) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Run the Action method
        try {
            $instance = new $action($this->job);
            $result   = $instance->{$method}();
        } catch (WorkflowsException $e) {
            return $this->renderError($e->getMessage());
        }

        // If it was a Response then we are done
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        // Null means the Stage is complete
        return $this->progress();
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
        // Verify the Action
        $actionId = array_shift($params);

        try {
            $action = ActionFactory::find($actionId);
        } catch (RuntimeException $e) {
            throw WorkflowsException::forActionNotFound();
        }

        // Verify the Job
        $jobId = array_shift($params);
        if (empty($jobId) || ! is_numeric($jobId)) {
            throw WorkflowsException::forMissingJobId($actionId);
        }
        if (null === $job = $this->jobs->find($jobId)) {
            throw WorkflowsException::forJobNotFound();
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
     * Checks if the current user can access an Action.
     */
    protected function checkAccess(string $action): bool
    {
        $role = $action::getAttributes()['role'] ?? '';

        // Allow public Actions
        if ($role === '') {
            return true;
        }

        // Check for a current user
        if (null === $user = service('users')->findById(user_id())) {
            return false;
        }

        /** @var HasPermission $user */
        return $user->hasPermission($role);
    }

    /**
     * Progresses a Job after an Action indicates
     * that the current Stage is done.
     */
    protected function progress(): ResponseInterface
    {
        // Get the next Stage
        if ($stage = $this->job->next()) {
            // Travel to the next Action
            $this->job->travel($stage->action_id, false);

            return redirect()->to($stage->getRoute() . $this->job->id);
        }

        // Update the Job as complete
        $this->jobs->update($this->job->id, ['stage_id' => null]);

        return $this->renderMessage(lang('Workflows.jobComplete', $this->job->name));
    }
}

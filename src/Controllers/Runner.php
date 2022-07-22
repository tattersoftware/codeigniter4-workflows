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

/**
 * Runner Controller
 *
 * Functions as a super-controller, routing to the specific
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

        // If the Job is completed then redirect the the job
        if ($this->job->stage_id === null) {
            return redirect()->to($this->job->getUrl())->with('message', lang('Workflows.jobAlreadyComplete'));
        }

        return redirect()->to(site_url($this->job->getStage()->getRoute() . $this->job->id));
    }

    /**
     * Receives route input and handles action coordination.
     *
     * @param string ...$params Parameters coming from the router (so all strings)
     *
     * @throws PageNotFoundException
     * @throws RuntimeException
     */
    public function run(string ...$params): ResponseInterface
    {
        if (empty($params)) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Verify the Action
        $actionId = array_shift($params);

        try {
            $action = ActionFactory::find($actionId);
        } catch (RuntimeException $e) {
            return $this->renderError(lang('Workflows.actionNotFound'));
        }

        // Verify the Job
        $jobId = array_shift($params);
        if (empty($jobId) || ! is_numeric($jobId)) {
            return $this->renderError(lang('Workflows.routeMissingJobId', [$actionId]));
        }
        if (null === $job = $this->jobs->withDeleted()->find($jobId)) {
            return $this->renderError(lang('Workflows.jobNotFound'));
        }

        $this->setJob($job);
        if ($this->job->deleted_at !== null) {
            return $this->renderError(lang('Workflows.useDeletedJob'));
        }

        // Intercept Jobs that are already completed
        if ($this->job->stage_id === null) {
            return redirect()->to($this->job->getUrl())->with('message', lang('Workflows.jobAlreadyComplete'));
        }

        $workflow = $this->job->getWorkflow();

        // If the requested Action differs from the Job's current Action then try to travel
        if ($action::HANDLER_ID !== $this->job->getStage()->action_id) {
            try {
                $stage = $workflow->getStageByAction($action::HANDLER_ID);
                $workflow->travel($this->job, $stage);
            } catch (WorkflowsException $e) {
                return $this->renderError($e->getMessage());
            }
        }

        // Check the Action's role against a potential current user
        if (! $this->checkActionAccess($action)) {
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

        // Null result means the Stage is complete; progress the Job
        try {
            $workflow->progress($job);
        } catch (WorkflowsException $e) {
            return $this->renderError($e->getMessage());
        }

        // Check if this completed the Job
        if ($this->job->getStage() === null) {
            return $this->renderMessage(lang('Workflows.jobComplete', [$this->job->name]));
        }

        // Send to the next Stage
        return redirect()->to(site_url($this->job->getStage()->getRoute() . $this->job->id));
    }

    /**
     * Checks if the current user can access an Action.
     */
    protected function checkActionAccess(string $action): bool
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
}

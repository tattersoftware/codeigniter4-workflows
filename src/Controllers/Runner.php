<?php

namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;
use Tatter\Workflows\BaseAction;
use Tatter\Workflows\Entities\Action;
use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Factories\ActionFactory;
use Tatter\Workflows\Interfaces\OwnsJob;

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

        // Check User permission - requires enforced authentication
        if (
            ($user = $this->getUser())
            && $user instanceof OwnsJob
            && ! $user->ownsJob($job)
        ) {
            return $this->renderError(lang('Workflows.jobNotAllowed'));
        }

        // Process the Job, displaying any Workflow exceptions as errors
        $this->setJob($job);

        try {
            return $this->handleResponse($this->applyAction($action));
        } catch (WorkflowsException $e) {
            return $this->renderError($e->getMessage());
        }
    }

    /**
     * Processes the Action on the Job as determined in the run parameters.
     *
     * @param class-string<BaseAction> $action
     *
     * @throws PageNotFoundException
     * @throws WorkflowsException
     */
    private function applyAction(string $action): ?ResponseInterface
    {
        if ($this->job->deleted_at !== null) {
            return $this->renderError(lang('Workflows.useDeletedJob'));
        }

        // Intercept Jobs that are already completed
        if ($this->job->stage_id === null) {
            return redirect()->to($this->job->getUrl())->with('message', lang('Workflows.jobAlreadyComplete'));
        }

        // If the requested Action differs from the Job's current Action then try to travel
        if ($action::HANDLER_ID !== $this->job->getStage()->action_id) {
            $workflow = $this->job->getWorkflow();
            $stage    = $workflow->getStageByAction($action::HANDLER_ID);
            $workflow->travel($this->job, $stage);
        }

        // Check the Action's role against the current user
        if (! $action::allowsUser($this->getUser())) {
            return $this->renderMessage(lang('Workflows.jobAwaitingInput', $this->job->name));
        }

        // Determine the request method and verify the corresponding Action method exists
        $method = $this->request->getMethod(); // @phpstan-ignore-line
        if (! method_exists($action, $method)) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Run the Action method
        $instance = new $action($this->job);

        return $instance->{$method}();
    }

    /**
     * Handles the Action response.
     *
     * @throws WorkflowsException
     */
    private function handleResponse(?ResponseInterface $response): ResponseInterface
    {
        // If it was a Response then we are done
        if ($response instanceof ResponseInterface) {
            return $response;
        }

        // Null result means the Stage is complete; progress the Job
        $this->job->getWorkflow()->progress($this->job);

        // Check if this completed the Job
        if ($this->job->getStage() === null) {
            return $this->renderMessage(lang('Workflows.jobComplete', [$this->job->name]));
        }

        // Send to the next Stage
        return redirect()->to(site_url($this->job->getStage()->getRoute() . $this->job->id));
    }
}

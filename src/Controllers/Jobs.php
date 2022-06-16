<?php

namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Models\JoblogModel;
use Tatter\Workflows\Models\WorkflowModel;

/**
 * Job Controller.
 *
 * Handles basic REST for Jobs.
 * Shares baseRoute with the Runner.
 */
class Jobs extends BaseController
{
    /**
     * Display a job.
     *
     * @param string $jobId ID of the job (int)
     *
     * @throws WorkflowsException
     */
    public function show(?string $jobId = null): ResponseInterface
    {
        // Load the job
        if (! $job = $this->jobs->withDeleted()->find($jobId)) {
            return $this->handleError(WorkflowsException::forJobNotFound());
        }

        $this->response->setBody(view($this->config->views['job'], [
            'job'    => $job,
            'logs'   => model(JoblogModel::class)->findWithStages($job->id), // @phpstan-ignore-line
            'layout' => config('Layouts')->public,
        ]));

        return $this->response;
    }

    /**
     * Start a new Job in the given Workflow.
     *
     * @param int|string|null $workflowId ID of the Workflow to use for the new Job (int)
     *
     * @throws WorkflowsException
     */
    public function new($workflowId = null): ResponseInterface
    {
        // If no Workflow was specified then load available
        if ($workflowId === null) {
            // Find available Workflows
            $workflows = [];

            foreach (model(WorkflowModel::class)->findAll() as $workflow) {
                if ($workflow->mayAccess()) {
                    $workflows[] = $workflow;
                }
            }

            if ($workflows === []) {
                return $this->handleError(WorkflowsException::forNoWorkflowAvailable());
            }

            // If more than one Workflow was available then display a selection
            if (count($workflows) > 1) {
                $this->response->setBody(view($this->config->views['workflow'], [
                    'layout'    => config('Layouts')->public,
                    'workflows' => $workflows,
                ]));

                return $this->response;
            }

            $workflow = reset($workflows);
        } elseif (! $workflow = model(WorkflowModel::class)->find($workflowId)) {
            return $this->handleError(WorkflowsException::forWorkflowNotFound());
        }

        // Verify access
        if (! $workflow->mayAccess()) {
            return $this->handleError(WorkflowsException::forWorkflowNotPermitted());
        }

        // Determine the starting point
        if (! $stages = $workflow->stages) {
            return $this->handleError(WorkflowsException::forMissingStages());
        }

        $stage = reset($stages);

        // Create the Job
        $jobId = $this->jobs->insert([
            'name'        => 'My New Job',
            'workflow_id' => $workflow->id,
            'stage_id'    => $stage->id,
        ]);

        // Send to the first action
        $action = $stage->action;
        $route  = "/{$this->config->routeBase}/{$action->uid}/{$jobId}";

        return redirect()->to(site_url($route))->with('success', lang('Workflows.newJobSuccess'));
    }

    /**
     * Deletes a job.
     *
     * @param string $jobId ID of the job to remove (int)
     *
     * @throws PageNotFoundException
     *
     * @return ResponseInterface a view notifying the user that the job was removed
     */
    public function delete(string $jobId): ResponseInterface
    {
        // Verify the job
        if (! $job = $this->jobs->find($jobId)) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Delete the job (soft)
        $this->jobs->delete($jobId);

        $this->response->setBody(view($this->config->views['deleted'], [
            'layout' => config('Layouts')->public,
            'job'    => $job,
        ]));

        return $this->response;
    }
}

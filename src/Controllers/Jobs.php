<?php

namespace Tatter\Workflows\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Workflows\Models\JoblogModel;
use Tatter\Workflows\Models\WorkflowModel;

/**
 * Handles basic CRUD for Jobs.
 */
class Jobs extends BaseController
{
    /**
     * Display a job.
     *
     * @param string $jobId ID of the job (int)
     */
    public function show(?string $jobId = null): ResponseInterface
    {
        // Load the job
        if (! $job = $this->jobs->withDeleted()->find($jobId)) {
            return $this->renderError(lang('Workflows.jobNotFound'));
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
     */
    public function new($workflowId = null): ResponseInterface
    {
        $user = $this->getUser();

        // If no Workflow was specified then load available
        if ($workflowId === null) {
            // Find available Workflows
            $workflows = [];

            foreach (model(WorkflowModel::class)->findAll() as $workflow) {
                if ($workflow->allowsUser($user)) {
                    $workflows[] = $workflow;
                }
            }

            if ($workflows === []) {
                return $this->renderError(lang('Workflows.noWorkflowAvailable'));
            }

            // If more than one Workflow was available then display a selection
            if (count($workflows) > 1) {
                return $this->render($this->config->views['workflow'], [
                    'workflows' => $workflows,
                ]);
            }

            $workflow = reset($workflows);
        } elseif (! $workflow = model(WorkflowModel::class)->find($workflowId)) {
            return $this->renderError(lang('Workflows.workflowNotFound'));
        }

        // Verify access
        if (! $workflow->allowsUser($user)) {
            return $this->renderError(lang('Workflows.workflowNotPermitted'));
        }

        // Determine the starting point
        if (! $stages = $workflow->getStages()) {
            return $this->renderError(lang('Workflows.workflowNoStages'));
        }

        $stage = reset($stages);

        // Create the Job
        $jobId = $this->jobs->insert([
            'name'        => 'My New Job',
            'workflow_id' => $workflow->id,
            'stage_id'    => $stage->id,
        ]);

        // Send to the first action
        return redirect()->to(site_url($stage->getRoute() . $jobId))->with('success', lang('Workflows.newJobSuccess'));
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
        if (! $this->job = $this->jobs->find($jobId)) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Delete the job (soft)
        $this->jobs->delete($jobId);

        return $this->renderMessage(lang('Workflows.jobDeleted', $this->job->name));
    }
}

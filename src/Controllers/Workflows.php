<?php

namespace Tatter\Workflows\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use Tatter\Workflows\Factories\ActionFactory;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;

class Workflows extends BaseController
{
    /**
     * Displays a list of available Workflows.
     */
    public function index(): string
    {
        /** @var WorkflowModel $workflows */
        $workflows = model(WorkflowModel::class);

        $data = [
            'layout'    => config('Layouts')->manage,
            'workflows' => $workflows->orderBy('name')->findAll(),
        ];

        // Prefetch the stages
        $data['stages'] = $workflows->fetchStages($data['workflows']);

        return view('Tatter\Workflows\Views\workflows\index', $data);
    }

    /**
     * Shows details for one Workflow.
     */
    public function show(string $workflowId): string
    {
        $data = [
            'config'    => config('Workflows'),
            'layout'    => config('Layouts')->manage,
            'workflow'  => model(WorkflowModel::class)->find($workflowId),
            'workflows' => model(WorkflowModel::class)->orderBy('name', 'asc')->findAll(),
            'actions'   => ActionFactory::getAllAttributes(),
        ];

        // Add the stages
        $data['stages'] = $data['workflow']->stages;

        return view('Tatter\Workflows\Views\workflows\show', $data);
    }

    /**
     * Displays the form for a new Workflow.
     */
    public function new(): string
    {
        $data = [
            'layout'  => config('Layouts')->manage,
            'actions' => ActionFactory::getAllAttributes(),
        ];

        // Prepare action data to be JSON encoded for JSSortable
        $json = [];

        foreach ($data['actions'] as $action) {
            $json[$action['id']] = $action;
        }

        $data['json'] = json_encode($json);

        return view('Tatter\Workflows\Views\workflows\new', $data);
    }

    /**
     * Creates a Workflow from the new form data.
     */
    public function create(): RedirectResponse
    {
        // Validate
        $rules = [
            'name'    => 'required|max_length[255]',
            'summary' => 'required|max_length[255]',
            'actions' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Try to create the workflow
        $workflow = $this->request->getPost();
        if (! $workflowId = model(WorkflowModel::class)->insert($workflow, true)) {
            return redirect()->back()->withInput()->with('errors', model(WorkflowModel::class)->errors());
        }

        // Create action-to-workflow stages
        foreach (explode(',', $this->request->getPost('actions')) as $actionId) {
            $stage = [
                'workflow_id' => $workflowId,
                'action_id'   => $actionId,
            ];

            model(StageModel::class)->insert($stage);
        }

        return redirect()->to('/workflows/' . $workflowId)->with('success', lang('Workflows.newWorkflowSuccess'));
    }

    /**
     * Update workflow details.
     */
    public function update(string $workflowId): RedirectResponse
    {
        // Validate
        $rules = [
            'name'    => 'required|max_length[255]',
            'summary' => 'required|max_length[255]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // try to update the workflow
        $workflow = $this->request->getPost();
        if (! model(WorkflowModel::class)->update($workflowId, $workflow)) {
            return redirect()->back()->withInput()->with('errors', model(WorkflowModel::class)->errors());
        }

        return redirect()->to('/workflows/' . $workflowId)->with('success', lang('Workflows.updateWorkflowSuccess'));
    }

    /**
     * Delete the workflow (soft).
     */
    public function delete(string $workflowId): RedirectResponse
    {
        model(WorkflowModel::class)->delete($workflowId);

        return redirect()->to('/workflows')->with('success', lang('Workflows.deletedWorkflowSuccess'));
    }
}

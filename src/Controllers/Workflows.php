<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\WorkflowModel;

class Workflows extends Controller
{
    /**
     * Displays a list of available Workflows.
     *
     * @return string
     */
    public function index(): string
    {
        $data = [
            'layout'    => config('Workflows')->layouts['manage'],
            'workflows' => model(WorkflowModel::class)->orderBy('name')->findAll(),
        ];

        // Prefetch the stages
        $data['stages'] = model(WorkflowModel::class)->fetchStages($data['workflows']);

        return view('Tatter\Workflows\Views\workflows\index', $data);
    }

    /**
     * Shows details for one Workflow.
     *
     * @param string $workflowId
     *
     * @return string
     */
    public function show(string $workflowId): string
    {
        $data = [
            'config'    => config('Workflows'),
            'layout'    => config('Workflows')->layouts['manage'],
            'workflow'  => model(WorkflowModel::class)->find($workflowId),
            'workflows' => model(WorkflowModel::class)->orderBy('name', 'asc')->findAll(),
            'actions'   => model(ActionModel::class)->orderBy('category', 'asc')->orderBy('name', 'asc')->findAll(),
        ];

        // Add the stages
        $data['stages'] = $data['workflow']->stages;

        return view('Tatter\Workflows\Views\workflows\show', $data);
    }

    /**
     * Displays the form for a new Workflow.
     *
     * @return string
     */
    public function new(): string
    {
        $data = [
            'layout'  => config('Workflows')->layouts['manage'],
            'actions' => model(ActionModel::class)->orderBy('category', 'asc')->orderBy('name', 'asc')->findAll(),
        ];

        // Prepare action data to be JSON encoded for JSSortable
        $json = [];
        foreach ($data['actions'] as $action) {
            $json[$action->id] = $action->toArray();
        }

        $data['json'] = json_encode($json);

        return view('Tatter\Workflows\Views\workflows\new', $data);
    }

    /**
     * Creates a Workflow from the new form data.
     *
     * @return RedirectResponse
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
     *
     * @param string $workflowId
     *
     * @return RedirectResponse
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
     *
     * @param string $workflowId
     *
     * @return RedirectResponse
     */
    public function delete(string $workflowId): RedirectResponse
    {
        model(WorkflowModel::class)->delete($workflowId);

        return redirect()->to('/workflows')->with('success', lang('Workflows.deletedWorkflowSuccess'));
    }
}

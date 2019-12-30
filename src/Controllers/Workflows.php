<?php namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Models\WorkflowModel;

class Workflows extends Controller
{
	// Load the common dependencies
	public function __construct()
	{
		$this->model  = new WorkflowModel();
		$this->stages = new StageModel();
		$this->tasks  = new TaskModel();
		$this->config = config('Workflows');
	}

	// Displays a list of available workflows.
	public function index(): string
	{
		$data = [
			'layout'    => $this->config->layouts['manage'],
			'workflows' => $this->model->orderBy('name')->findAll(),
		];
		
		// Prefetch the stages
		$data['stages'] = $this->model->fetchStages($data['workflows']);
		
		return view('Tatter\Workflows\Views\workflows\index', $data);
	}

	// Shows details for one workflow
	public function show(string $workflowId): string
	{
		$data = [
			'config'    => $this->config,
			'layout'    => $this->config->layouts['manage'],
			'workflow'  => $this->model->find($workflowId),
			'workflows' => $this->model->orderBy('name', 'asc')->findAll(),
			'tasks'     => $this->tasks->orderBy('category', 'asc')->orderBy('name', 'asc')->findAll(),
		];

		// Add the stages
		$data['stages'] = $data['workflow']->stages;

		return view('Tatter\Workflows\Views\workflows\show', $data);
	}

	// Display the form for a new workflow
	public function new(): string
	{
		$data = [
			'layout' => $this->config->layouts['manage'],
			'tasks'  => $this->tasks->orderBy('category', 'asc')->orderBy('name', 'asc')->findAll(),
		];
		
		// Prepare task data to be JSON encoded for JSSortable
		$json = [];
		foreach ($data['tasks'] as $task)
		{
			$json[$task->id] = $task->toArray();
		}
		
		$data['json'] = json_encode($json);
		
		return view('Tatter\Workflows\Views\workflows\new', $data);		
	}

	// Create a workflow from the new form data
	public function create(): RedirectResponse
	{		
		// Validate
		$rules = [
			'name'    => 'required|max_length[255]',
			'summary' => 'required|max_length[255]',
			'tasks'   => 'required',
		];

		if (! $this->validate($rules))
		{
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}

		// Try to create the workflow
		$workflow = $this->request->getPost();
		if (! $workflowId = $this->model->insert($workflow, true))
		{
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        // Create task-to-workflow stages
		foreach (explode(',', $this->request->getPost('tasks')) as $taskId)
		{
			$stage = [
				'workflow_id' => $workflowId,
				'task_id'     => $taskId,
			];

			$this->stages->insert($stage);
		}

		return redirect()->to('/workflows/' . $workflowId)->with('success', lang('Workflows.newWorkflowSuccess'));
	}

	// Update workflow details
	public function update(string $workflowId): RedirectResponse
	{		
		// validate
		$rules = [
			'name'     => 'required|max_length[255]',
			'summary'  => 'required|max_length[255]',
		];
		if (! $this->validate($rules))
		{
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		
		// try to update the workflow
		$workflow = $this->request->getPost();
		if (! $this->model->update($workflowId, $workflow))
		{
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }
        		
		return redirect()->to('/workflows/' . $workflowId)->with('success', lang('Workflows.updateWorkflowSuccess'));
	}

	// Delete the workflow (soft)
	public function delete($workflowId): RedirectResponse
	{		
		// (Soft) delete the workflow
		$this->model->delete($workflowId);

		return redirect()->to('/workflows')->with('success', lang('Workflows.deletedWorkflowSuccess'));
	}
}

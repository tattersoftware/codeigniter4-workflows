<?php namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Config\Services;
use Tatter\Workflows\Entities\Task;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Models\WorkflowModel;

class Workflows extends Controller
{
	public function __construct()
	{
		$this->model  = new WorkflowModel();
		$this->stages = new StageModel();
		$this->tasks  = new TaskModel();
		$this->config = config('Workflows');
	}
	
	public function index()
	{
		$data['layout']    = $this->config->layouts['public'];
		$data['workflows'] = $this->model->orderBy('name')->findAll();
		$data['stages']    = $this->model->fetchStages($data['workflows']);
		
		return view('Tatter\Workflows\Views\workflows\index', $data);
	}
	
	public function show($workflowId)
	{
		$data['config']    = $this->config;
		$data['layout']    = $this->config->layouts['public'];
		$data['workflow']  = $this->model->find($workflowId);
		$data['workflows'] = $this->model->orderBy('name', 'asc')->findAll();
		$data['stages']    = $data['workflow']->stages;
		$data['tasks']     = $this->tasks
			->orderBy('category', 'asc')
			->orderBy('name', 'asc')
			->findAll();

		return view('Tatter\Workflows\Views\workflows\show', $data);
	}
	
	public function new()
	{
		$data['layout'] = $this->config->layouts['public'];
		$data['tasks']  = $this->tasks
			->orderBy('category', 'asc')
			->orderBy('name', 'asc')
			->findAll();
		
		// prepare task data for json_encode
		$json = [ ];
		foreach ($data['tasks'] as $task)
		{
			$json[$task->id] = $task->toArray();
		}
		
		$data['json'] = json_encode($json);
		
		return view('Tatter\Workflows\Views\workflows\new', $data);		
	}
	
	public function create()
	{		
		// validate
		$rules = [
			'name'     => 'required|max_length[255]',
			'summary'  => 'required|max_length[255]',
			'tasks'    => 'required',
		];
		if (! $this->validate($rules))
		{
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		}
		
		// Try to create the workflow
		$workflow = $this->request->getPost();
		if (! $this->model->save($workflow))
		{
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }
        
        // Create task-to-workflow stages
		$workflowId = $this->model->getInsertID();
		$tasks = explode(',', $this->request->getPost('tasks'));
		
		foreach ($tasks as $taskId)
		{
			$stage = [
				'workflow_id' => $workflowId,
				'task_id'     => $taskId,
			];
			$this->stages->save($stage);
		}
		
		return redirect()->to('/workflows/' . $workflowId)->with('success', lang('Workflows.newWorkflowSuccess'));
	}
	
	public function update($workflowId)
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
	
	public function delete($workflowId)
	{		
		// (Soft) delete the workflow
		$this->model->delete($workflowId);

		return redirect()->to('/workflows')->with('success', lang('Workflows.deletedWorkflowSuccess'));
	}
}

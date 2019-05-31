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
		
		$this->config = class_exists('\Config\Workflows') ?
			new \Config\Workflows() : new \Tatter\Workflows\Config\Workflows();
	}
	
	public function index()
	{
		$data['config'] = $this->config;
		$data['workflows'] = $this->model->orderBy('name')->findAll();
		$data['stages'] = $this->model->fetchStages($data['workflows']);
		
		return view('Tatter\Workflows\Views\workflows\index', $data);
	}
	
	public function show($workflowId)
	{
		$data['config'] = $this->config;
		$data['workflow'] = $this->model->find($workflowId);
		$data['workflows'] = $this->model->orderBy('name', 'asc')->findAll();
		$data['stages'] = $data['workflow']->stages;
		$data['tasks'] = $this->tasks
			->orderBy('category', 'asc')
			->orderBy('name', 'asc')
			->findAll();

		return view('Tatter\Workflows\Views\workflows\show', $data);
	}
	
	public function new()
	{
		$data['config'] = $this->config;
		$data['tasks'] = $this->tasks
			->orderBy('category', 'asc')
			->orderBy('name', 'asc')
			->findAll();
		
		// prepare task data for json_encode
		$json = [ ];
		foreach ($data['tasks'] as $task)
			$json[$task->id] = $task->toArray();
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
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		
		// try to create the workflow
		$workflow = $this->request->getPost();
		if (! $this->model->save($workflow))
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        
        // create task-to-workflow stages
		$workflowId = $this->model->getInsertID();
		$tasks = explode(',', $this->request->getPost('tasks'));
		foreach ($tasks as $taskId):
			$stage = [
				'workflow_id' => $workflowId,
				'task_id'     => $taskId,
			];
			$this->stages->save($stage);
		endforeach;
		
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
			return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
		
		// try to update the workflow
		$workflow = $this->request->getPost();
		if (! $this->model->update($workflowId, $workflow))
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        		
		return redirect()->to('/workflows/' . $workflowId)->with('success', lang('Workflows.updateWorkflowSuccess'));
	}
}

<?php namespace Tatter\Workflows\Tasks;

use Tatter\Workflows\Entities\Task;
use Tatter\Workflows\Interfaces\TaskInterface;
use Tatter\Workflows\Models\TaskModel;
use Tatter\Workflows\Models\WorkflowModel;

class InfoTask implements TaskInterface
{
	use \Tatter\Workflows\Traits\TasksTrait;
	
	public $definition = [
		'category' => 'Core',
		'name'     => 'Info',
		'uid'      => 'info',
		'icon'     => 'fas fa-info-circle',
		'summary'  => 'Set basic details of a job',
	];
	
	// display the edit form
	public function get()
	{
		// prep the view and return it
		$this->renderer->setVar('layout', $this->config->layout);
		$this->renderer->setVar('config', $this->config);
		$this->renderer->setVar('job', $this->job);

		return $this->renderer->render('Tatter\Workflows\Views\tasks\info');
	}
	
	// validate and process form submission
	public function post()
	{
		// validate
		$rules = [
			'name'     => 'required|max_length[255]',
			'summary'  => 'max_length[255]',
		];
		$valid = $this->validation
			->setRules($rules)
			->withRequest($this->request)
			->run();
		if (! $valid)
			return redirect()->back()->withInput()->with('errors', $this->validation->getErrors());
		
		// try to update the job
		$row = $this->request->getPost();
		if (! $this->jobs->update($this->job->id, $row))
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        		
		return true;
	}
	
	// run when a job progresses forward through the workflow
	public function up()
	{
	
	}
	
	// run when job regresses back through the workflow
	public function down()
	{
	
	}
}

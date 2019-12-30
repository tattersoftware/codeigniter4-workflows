<?php namespace Tatter\Workflows\Tasks;

use Tatter\Workflows\Interfaces\TaskInterface;

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
	
	// Display the edit form
	public function get(): string
	{
		// Prep the view and return it
		$this->renderer->setVar('layout', $this->config->layouts['public']);
		$this->renderer->setVar('config', $this->config);
		$this->renderer->setVar('job', $this->job);

		return $this->renderer->render('Tatter\Workflows\Views\tasks\info');
	}
	
	// Validate and process form submission
	public function post()
	{
		// Validate
		$rules = [
			'name'     => 'required|max_length[255]',
			'summary'  => 'max_length[255]',
		];

		if (! $this->validation->setRules($rules)->withRequest($this->request)->run())
		{
			return redirect()->back()->withInput()->with('errors', $this->validation->getErrors());
		}

		// Try to update the job
		if (! $this->jobs->update($this->job->id, $this->request->getPost()))
		{
            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

		return true;
	}
	
	// Run when a job progresses forward through the workflow
	public function up()
	{

	}
	
	// run when job regresses back through the workflow
	public function down()
	{

	}
}

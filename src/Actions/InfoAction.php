<?php namespace Tatter\Workflows\Actions;

use Tatter\Workflows\BaseAction;

class InfoAction extends BaseAction
{
	public $definition = [
		'category' => 'Core',
		'name'     => 'Info',
		'uid'      => 'info',
		'role'     => 'user',
		'icon'     => 'fas fa-info-circle',
		'summary'  => 'Set basic details of a job',
	];
	
	// Display the edit form
	public function get(): string
	{
		return view('Tatter\Workflows\Views\actions\info', [
			'layout' => $this->config->layouts['public'],
			'config' => $this->config,
			'job'    => $this->job,
		]);
	}
	
	/**
	 * Validates and processes form submission.
	 *
	 * @return \CodeIgniter\HTTP\RedirectResponse|bool
	 */
	public function post()
	{
		// Validate
		$validation = service('validation')->reset()->setRules([
			'name'     => 'required|max_length[255]',
			'summary'  => 'max_length[255]',
		]);

		if (! $validation->withRequest($this->request)->run())
		{
			return redirect()->back()->withInput()->with('errors', $validation->getErrors());
		}

		// Try to update the job
		if (! $this->jobs->update($this->job->id, $this->request->getPost()))
		{
            return redirect()->back()->withInput()->with('errors', $this->jobs->errors());
        }

		return true;
	}
}

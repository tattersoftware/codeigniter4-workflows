<?php namespace Tatter\Workflows\Actions;

use Tatter\Workflows\BaseAction;

class InfoAction extends BaseAction
{
	public $attributes = [
		'category' => 'Core',
		'name'     => 'Info',
		'uid'      => 'info',
		'role'     => '',
		'icon'     => 'fas fa-info-circle',
		'summary'  => 'Set basic details of a job',
	];

	/**
	 * Display the edit form.
	 *
	 * @return string The view
	 */
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
	 * @return \CodeIgniter\HTTP\RedirectResponse|boolean
	 */
	public function post()
	{
		// Validate
		$validation = service('validation')->reset()->setRules([
			'name'    => 'required|max_length[255]',
			'summary' => 'max_length[255]',
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

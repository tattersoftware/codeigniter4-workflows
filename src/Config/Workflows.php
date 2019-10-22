<?php namespace Tatter\Workflows\Config;

use CodeIgniter\Config\BaseConfig;

class Workflows extends BaseConfig
{
	// Whether to continue instead of throwing exceptions
	public $silent = true;
	
	// Faux-controller to route off of
	public $routeBase = 'jobs';
	
	// The session variable to check for a logged-in user ID
	public $userSource = 'logged_in';
	
	// The model to use for jobs
	public $jobModel = 'Tatter\Workflows\Models\JobModel';
	
	// Layouts to use for jobs and administration
	public $layouts = [
		'public' => 'layouts/public',
		'manage' => 'layouts/manage',
	];
	
	// Views to display for each function
	public $views = [
		'messages'  => 'Tatter\Workflows\Views\messages',
		'complete'  => 'Tatter\Workflows\Views\complete',
		'deleted'   => 'Tatter\Workflows\Views\deleted',
	];
}

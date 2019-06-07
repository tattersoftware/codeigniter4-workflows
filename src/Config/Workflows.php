<?php namespace Tatter\Workflows\Config;

use CodeIgniter\Config\BaseConfig;

class Workflows extends BaseConfig
{
	// whether to continue instead of throwing exceptions
	public $silent = true;
	
	// faux-controller to route off of
	public $routeBase = 'jobs';
	
	// the session variable to check for a logged-in user ID
	public $userSource = 'workflowsUserId';
	
	// views to display for each function
	public $views = [
		'header'    => 'Tatter\Workflows\Views\templates\header',
		'footer'    => 'Tatter\Workflows\Views\templates\footer',
		'messages'  => 'Tatter\Workflows\Views\messages',
	];
}

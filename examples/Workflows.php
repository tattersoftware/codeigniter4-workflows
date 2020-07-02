<?php namespace Config;

/***
*
* This file contains example values to alter default library behavior.
* Recommended usage:
*	1. Copy the file to app/Config/Workflows.php
*	2. Change any values
*	3. Remove any lines to fallback to defaults
*
***/

class Workflows extends \Tatter\Workflows\Config\Workflows
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
		'filter'    => 'Tatter\Workflows\Views\filter',
	];
}

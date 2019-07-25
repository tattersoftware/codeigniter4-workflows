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
	// whether to continue instead of throwing exceptions
	public $silent = true;
	
	// faux-controller to route off of
	public $routeBase = 'jobs';
	
	// the session variable to check for a logged-in user ID
	public $userSource = 'logged_in';
	
	// the model to use for jobs
	public $jobModel = 'Tatter\Workflows\Models\JobModel';
	
	// views to display for each function
	public $views = [
		'header'    => 'Tatter\Workflows\Views\templates\header',
		'footer'    => 'Tatter\Workflows\Views\templates\footer',
		'messages'  => 'Tatter\Workflows\Views\messages',
		'complete'  => 'Tatter\Workflows\Views\complete',
		'deleted'   => 'Tatter\Workflows\Views\deleted',
	];
}

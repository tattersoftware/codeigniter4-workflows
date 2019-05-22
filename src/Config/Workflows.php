<?php namespace Tatter\Workflows\Config;

use CodeIgniter\Config\BaseConfig;

class Workflows extends BaseConfig
{
	// whether to continue instead of throwing exceptions
	public $silent = true;
	
	// faux-controller to route off of
	public $routeBase = 'jobs';
	
	// views to display for each function
	public $views = [
		'header'    => 'Tatter\Workflows\Views\header',
		'footer'    => 'Tatter\Workflows\Views\footer',
	];
}

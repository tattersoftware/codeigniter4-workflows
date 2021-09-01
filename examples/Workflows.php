<?php

namespace Config;

/*
 *
 * This file contains example values to alter default library behavior.
 * Recommended usage:
 *	1. Copy the file to app/Config/Workflows.php
 *	2. Change any values
 *	3. Remove any lines to fallback to defaults
 *
 */

class Workflows extends \Tatter\Workflows\Config\Workflows
{
	/**
	 * Whether to continue instead of throwing exceptions.
	 *
	 * @var bool
	 */
	public $silent = ENVIRONMENT === 'production';

	/**
	 * Route base to use for Runner Controller.
	 *
	 * @var string
	 */
	public $routeBase = 'jobs';

	/**
	 * The model to use for jobs.
	 *
	 * @var string
	 */
	public $jobModel = JobModel::class;

	/**
	 * View layouts to use for jobs and administration.
	 * Needs to have keys "public" and "manage".
	 *
	 * @var array<string,string>
	 */
	public $layouts = [
		'public' => 'Tatter\Workflows\Views\layout',
		'manage' => 'Tatter\Workflows\Views\layout',
	];

	/**
	 * Views to display for various function.
	 *
	 * @var array<string,string>
	 */
	public $views = [
		'job'      => 'Tatter\Workflows\Views\job',
		'messages' => 'Tatter\Workflows\Views\messages',
		'complete' => 'Tatter\Workflows\Views\complete',
		'deleted'  => 'Tatter\Workflows\Views\deleted',
		'filter'   => 'Tatter\Workflows\Views\filter',
	];
}

<?php

namespace Tatter\Workflows\Config;

use CodeIgniter\Config\BaseConfig;

class Workflows extends BaseConfig
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
	public $jobModel = 'Tatter\Workflows\Models\JobModel';

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
		'workflow' => 'Tatter\Workflows\Views\workflow',
		'messages' => 'Tatter\Workflows\Views\messages',
		'complete' => 'Tatter\Workflows\Views\complete',
		'deleted'  => 'Tatter\Workflows\Views\deleted',
		'filter'   => 'Tatter\Workflows\Views\filter',
	];
}

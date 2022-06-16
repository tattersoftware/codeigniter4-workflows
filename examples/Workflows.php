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
 
use Tatter\Workflows\Config\Workflows as BaseWorkflows;

class Workflows extends BaseWorkflows
{
    /**
     * Route base to use for Runner Controller.
     */
    public string $routeBase = 'jobs';

    /**
     * The model to use for jobs.
     *
     * @var class-string<JobModel>
     */
    public string $jobModel = JobModel::class;

    /**
     * Views to display for various function.
     *
     * @var array<string,string>
     */
    public array $views = [
        'job'      => 'Tatter\Workflows\Views\job',
        'workflow' => 'Tatter\Workflows\Views\workflow',
        'messages' => 'Tatter\Workflows\Views\messages',
        'complete' => 'Tatter\Workflows\Views\complete',
        'deleted'  => 'Tatter\Workflows\Views\deleted',
        'filter'   => 'Tatter\Workflows\Views\filter',
        'info'     => 'Tatter\Workflows\Views\actions\info',
    ];
}

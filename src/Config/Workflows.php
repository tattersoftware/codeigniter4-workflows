<?php

namespace Tatter\Workflows\Config;

use CodeIgniter\Config\BaseConfig;
use Tatter\Workflows\Models\JobModel;

class Workflows extends BaseConfig
{
    /**
     * Whether to continue instead of throwing exceptions.
     */
    public bool $silent = ENVIRONMENT === 'production';

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

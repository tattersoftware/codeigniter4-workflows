<?php

namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Workflows\Config\Workflows as WorkflowsConfig;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Models\JobModel;

abstract class BaseController extends Controller
{
    protected WorkflowsConfig $config;
    protected JobModel $jobs;

    /**
     * Preload the config class and Model for jobs.
     */
    public function __construct()
    {
        $this->config = config('Workflows');
        $this->jobs   = model($this->config->jobModel); // @phpstan-ignore-line
    }

    /**
     * Handles errors based on Config settings.
     *
     * @throws WorkflowsException
     *
     * @return ResponseInterface The error view, if silent mode
     */
    protected function handleError(WorkflowsException $exception): ResponseInterface
    {
        $this->response->setBody(view($this->config->views['messages'], [
            'layout' => config('Layouts')->public,
            'error'  => $exception->getMessage(),
        ]));

        return $this->response;
    }
}

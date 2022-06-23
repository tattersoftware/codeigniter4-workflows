<?php

namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Workflows\Config\Workflows as WorkflowsConfig;
use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Models\JobModel;

abstract class BaseController extends Controller
{
    protected WorkflowsConfig $config;
    protected ?Job $job = null;
    protected JobModel $jobs;

    /**
     * Preload the Config class and job Model.
     */
    public function __construct()
    {
        $this->config = config('Workflows');
        $this->jobs   = model($this->config->jobModel); // @phpstan-ignore-line
    }

    /**
     * Sets the current Job.
     *
     * @return $this
     */
    final protected function setJob(Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Renders the content within the workflow layout.
     *
     * @param string $view The view file
     * @param array  $data Any variable data to pass to View
     */
    protected function render(string $view, array $data = []): ResponseInterface
    {
        $data = array_merge([
            'layout' => config('Layouts')->public,
            'config' => $this->config,
            'job'    => $this->job,
        ], $data);

        return $this->response->setBody(view($view, $data));
    }

    /**
     * Handles errors based on Config settings.
     */
    protected function renderMessage(string $message, string $class = 'info', string $header = 'Status Message'): ResponseInterface
    {
        return $this->render($this->config->views['messages'], [
            'message' => $message,
            'header'  => $header,
            'class'   => $class,
        ]);
    }

    /**
     * Handles errors based on Config settings.
     */
    protected function renderSuccess(string $message): ResponseInterface
    {
        return $this->renderMessage($message, 'success');
    }

    /**
     * Handles errors based on Config settings.
     */
    protected function renderError(string $message): ResponseInterface
    {
        return $this->renderMessage($message, 'danger', 'Error Message');
    }
}

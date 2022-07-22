<?php

namespace Tatter\Workflows\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use RuntimeException;

class WorkflowsException extends RuntimeException implements ExceptionInterface
{
    /**
     * @return static
     */
    public static function forNoWorkflowAvailable(): self
    {
        return new static(lang('Workflows.noWorkflowAvailable'), 404);
    }

    /**
     * @return static
     */
    public static function forWorkflowNotFound(): self
    {
        return new static(lang('Workflows.workflowNotFound'), 404);
    }

    /**
     * @return static
     */
    public static function forWorkflowNotPermitted(): self
    {
        return new static(lang('Workflows.workflowNotPermitted'), 403);
    }

    /**
     * @return static
     */
    public static function forActionNotFound(): self
    {
        return new static(lang('Workflows.actionNotFound'), 404);
    }

    /**
     * @return static
     */
    public static function forJobNotFound(): self
    {
        return new static(lang('Workflows.jobNotFound'), 404);
    }

    /**
     * @return static
     */
    public static function forStageNotFound(): self
    {
        return new static(lang('Workflows.stageNotFound'));
    }

    /**
     * @return static
     */
    public static function forMissingStages(): self
    {
        return new static(lang('Workflows.workflowNoStages'));
    }

    /**
     * @param mixed $name
     *
     * @return static
     */
    public static function forSkipRequiredStage($name): self
    {
        return new static(lang('Workflows.skipRequiredStage', [$name]));
    }

    /**
     * @param mixed $route
     *
     * @return static
     */
    public static function forMissingJobId($route = ''): self
    {
        return new static(lang('Workflows.routeMissingJobId', [$route]));
    }

    /**
     * @param mixed $action
     *
     * @return static
     */
    public static function forUnsupportedActionMethod($action, string $method): self
    {
        return new static(lang('Workflows.actionMissingMethod', [$action, $method]));
    }
}

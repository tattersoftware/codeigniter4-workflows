<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Workflows\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use RuntimeException;

class WorkflowsException extends RuntimeException implements ExceptionInterface
{
    public static function forNoWorkflowAvailable()
    {
        return new static(lang('Workflows.noWorkflowAvailable'), 404);
    }

    public static function forWorkflowNotFound()
    {
        return new static(lang('Workflows.workflowNotFound'), 404);
    }

    public static function forWorkflowNotPermitted()
    {
        return new static(lang('Workflows.workflowNotPermitted'), 403);
    }

    public static function forActionNotFound()
    {
        return new static(lang('Workflows.actionNotFound'), 404);
    }

    public static function forJobNotFound()
    {
        return new static(lang('Workflows.jobNotFound'), 404);
    }

    public static function forStageNotFound()
    {
        return new static(lang('Workflows.stageNotFound'));
    }

    public static function forMissingStages()
    {
        return new static(lang('Workflows.workflowNoStages'));
    }

    public static function forSkipRequiredStage($name)
    {
        return new static(lang('Workflows.skipRequiredStage', [$name]));
    }

    public static function forMissingJobId($route = '')
    {
        return new static(lang('Workflows.routeMissingJobId', [$route]));
    }

    public static function forUnsupportedActionMethod($action, $method)
    {
        return new static(lang('Workflows.actionMissingMethod', [$action, $method]));
    }
}

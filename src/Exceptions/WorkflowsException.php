<?php namespace Tatter\Workflows\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class WorkflowsException extends \RuntimeException implements ExceptionInterface
{
	public static function forWorkflowNotFound()
	{
		return new static(lang('Workflows.workflowNotFound'), 404);
	}
	
	public static function forTaskNotFound()
	{
		return new static(lang('Workflows.taskNotFound'), 404);
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
	
	public static function forMissingJobId($route = '')
	{
		return new static(lang('Workflows.routeMissingJobId', [$route]));
	}
	
	public static function forUnsupportedTaskMethod($task, $method)
	{
		return new static(lang('Workflows.taskMissingMethod', [$task, $method]));
	}
}

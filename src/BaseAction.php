<?php

namespace Tatter\Workflows;

use Tatter\Workflows\Controllers\BaseController;
use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Exceptions\WorkflowsException;

/**
 * Common properties and methods for Actions. Classes may
 * implement any HTTP verb as a method (e.g. get(), put())
 * which should behave as follows:
 *  - User interactions: ResponseInterface
 *  - Action complete: null
 *  - Failure: throws WorkflowsException.
 * Actions extend Controller so have access to all the
 * usual tools like a typical controller.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
 */
abstract class BaseAction extends BaseController
{
    public const HANDLER_ID = '';

    /**
     * Action attributes to register in the database.
     * Set by child classes.
     *
     * @see getAttributes()
     *
     * @var array<string,scalar|null>
     */
    public const ATTRIBUTES = [];

    /**
     * Default set of attributes.
     *
     * @var array<string,scalar|null>
     */
    protected static array $defaults = [
        'id'       => '',
        'name'     => '',
        'role'     => '',
        'icon'     => 'fas fa-tasks',
        'category' => '',
        'summary'  => '',
        'class'    => '',
    ];

    /**
     * Returns this Action's attributes (including defaults).
     *
     * @return array<string,scalar|null>
     */
    final public static function getAttributes(): array
    {
        $attributes = array_merge(static::$defaults, static::ATTRIBUTES);

        $attributes['id']    = static::HANDLER_ID;
        $attributes['class'] = static::class;

        return $attributes;
    }

    /**
     * Runs on a Job when it progresses through the workflow.
     * May throw a WorkflowsException to halt and display a message.
     * Optionally implemented by child classes.
     */
    public static function up(Job $job): Job
    {
        return $job;
    }

    /**
     * Runs on a Job when it regresses through the workflow.
     * May throw a WorkflowsException to halt and display a message.
     * Optionally implemented by child classes.
     */
    public static function down(Job $job): Job
    {
        return $job;
    }

    //--------------------------------------------------------------------

    /**
     * Sets up common resources for Actions.
     * For extension use initialize() instead.
     */
    final public function __construct(Job $job)
    {
        parent::__construct();

        $this->initController(service('request'), service('response'), service('logger'));
        $this->setJob($job);
        $this->initialize();
    }

    /**
     * Initializes the instance with any additional steps.
     * Optionally implemented by child classes.
     */
    protected function initialize(): void
    {
    }
}

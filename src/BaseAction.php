<?php

namespace Tatter\Workflows;

use OutOfBoundsException;
use Tatter\Users\Interfaces\HasPermission;
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
     * Action attributes, set by child classes.
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
     * Returns a single attribute.
     *
     * @throws OutOfBoundsException
     *
     * @return scalar|null
     */
    final public static function attr(string $key)
    {
        $attributes = self::getAttributes();

        if (! array_key_exists($key, $attributes)) {
            throw new OutOfBoundsException('Attribute does not exist: ' . $key);
        }

        return $attributes[$key];
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

    /**
     * Checks whether a Job may skip this Stage. Allows skipping Stages
     * that are marked as "required" (if they meet method criteria).
     * Optionally implemented by child classes, e.g.:
     *   return $job->getFlag('Accepted') !== null;
     */
    public static function maySkip(Job $job): bool
    {
        return false;
    }

    /**
     * Checks whether a user may access this Action.
     */
    public static function allowsUser(?HasPermission $user): bool
    {
        $role = static::getAttributes()['role'];

        // Allow all public Actions
        if (empty($role)) {
            return true;
        }

        return $user === null
            ? false
            : $user->hasPermission($role);
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

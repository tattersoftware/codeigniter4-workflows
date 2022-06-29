<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

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
        'name'     => '',
        'role'     => 'user',
        'icon'     => 'fas fa-tasks',
        'category' => '',
        'summary'  => '',
    ];

    /**
     * Returns this Action's attributes (including defaults).
     *
     * @return array<string,scalar|null>
     */
    final public static function getAttributes(): array
    {
        $attributes = array_merge(static::$defaults, static::ATTRIBUTES);

        $attributes['uid']   = static::HANDLER_ID;
        $attributes['class'] = static::class;

        return $attributes;
    }

    //--------------------------------------------------------------------

    /**
     * Sets up common resources for Actions.
     * For extension use initialize() instead.
     */
    final public function __construct(Job $job)
    {
        parent::__construct();

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

    //--------------------------------------------------------------------

    /**
     * Runs when a job progresses forward through the workflow.
     * Optionally implemented by child classes.
     */
    public function up(): void
    {
    }

    /**
     * Runs when job regresses back through the workflow.
     * Optionally implemented by child classes.
     */
    public function down(): void
    {
    }
}

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

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Workflows\Config\Workflows as WorkflowsConfig;
use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Models\JobModel;

/**
 * Class reference and common method for
 * child Actions. Classes may implement any
 * HTTP verb as a method (e.g. get(), put())
 * which should behave as follows:
 *  - User interactions: ResponseInterface
 *  - Action complete: null
 *  - Failure: throws WorkflowsException.
 */
abstract class BaseAction
{
    public const HANDLER_ID = '';

    /**
     * Action attributes to register in the database.
     * Set by child classes.
     *
     * @see getAttributes()
     *
     * @var array<string,string|null>
     */
    public const ATTRIBUTES = [];

    /**
     * @var Job|null
     */
    public $job;

    /**
     * @var WorkflowsConfig
     */
    public $config;

    /**
     * @var RequestInterface
     */
    public $request;

    /**
     * @var ResponseInterface
     */
    public $response;

    /**
     * @var JobModel
     */
    public $jobs;

    /**
     * Default set of attributes.
     *
     * @var array<string,string>
     */
    protected static $defaults = [
        'name'     => '',
        'role'     => 'user',
        'icon'     => 'fas fa-tasks',
        'category' => '',
        'summary'  => '',
    ];

    /**
     * Returns this Action's attributes including defaults.
     *
     * @return array<string,string|null>
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
     */
    public function __construct(?Job $job = null, ?WorkflowsConfig $config = null, ?RequestInterface $request = null, ?ResponseInterface $response = null)
    {
        $this->job      = $job;
        $this->config   = $config ?? config('Workflows');
        $this->request  = $request ?? service('request');
        $this->response = $response ?? service('response');

        $this->jobs = model($this->config->jobModel); // @phpstan-ignore-line

        $this->initialize();
    }

    /**
     * Sets the Job for this Action to run against.
     * Used because Handlers needs to instantiate this
     * class without parameters.
     *
     * @return $this
     */
    public function setJob(Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    //--------------------------------------------------------------------

    /**
     * Runs when a job progresses forward through the workflow.
     *
     * @return mixed
     */
    public function up()
    {
        // Optionally implemented by child class
    }

    /**
     * Runs when job regresses back through the workflow.
     *
     * @return mixed
     */
    public function down()
    {
        // Optionally implemented by child class
    }

    //--------------------------------------------------------------------

    /**
     * HTTP Request Methods.
     *
     * These baseline defaults specify the expected
     * return types and exception behavior for the
     * supported HTTP methods. Child classes should
     * override these methods but follow the correct
     * method definition.
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
     */

    /**
     * @throws WorkflowsException
     */
    public function get(): ?ResponseInterface
    {
        throw new WorkflowsException('Not implemented.');
    }

    /**
     * @throws WorkflowsException
     */
    public function head(): ?ResponseInterface
    {
        throw new WorkflowsException('Not implemented.');
    }

    /**
     * @throws WorkflowsException
     */
    public function post(): ?ResponseInterface
    {
        throw new WorkflowsException('Not implemented.');
    }

    /**
     * @throws WorkflowsException
     */
    public function put(): ?ResponseInterface
    {
        throw new WorkflowsException('Not implemented.');
    }

    /**
     * @throws WorkflowsException
     */
    public function delete(): ?ResponseInterface
    {
        throw new WorkflowsException('Not implemented.');
    }

    /**
     * @throws WorkflowsException
     */
    public function connect(): ?ResponseInterface
    {
        throw new WorkflowsException('Not implemented.');
    }

    /**
     * @throws WorkflowsException
     */
    public function options(): ?ResponseInterface
    {
        throw new WorkflowsException('Not implemented.');
    }

    /**
     * @throws WorkflowsException
     */
    public function trace(): ?ResponseInterface
    {
        throw new WorkflowsException('Not implemented.');
    }

    /**
     * @throws WorkflowsException
     */
    public function patch(): ?ResponseInterface
    {
        throw new WorkflowsException('Not implemented.');
    }

    /**
     * Initializes the instance with any additional steps.
     * Optionally implemented by child classes.
     */
    protected function initialize()
    {
    }
}

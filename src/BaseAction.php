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
use RuntimeException;
use Tatter\Handlers\BaseHandler;
use Tatter\Workflows\Config\Workflows as WorkflowsConfig;
use Tatter\Workflows\Entities\Job;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\JobModel;

/**
 * Class reference and common method for
 * child Actions. Classes may implement any
 * HTTP verb as a method (e.g. get(), put())
 * which should behav as follows:
 *  - User interactions: ResponseInterface
 *  - Action complete: null
 *  - Failure: throws WorkflowsException.
 */
abstract class BaseAction extends BaseHandler
{
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
     * Attributes to Tatter\Handlers, implemented by child class.
     *
     * @var array<string>|null
     */
    protected $attributes = [];

    /**
     * Default set of attributes and their types.
     *
     * @var array<string>|null
     */
    protected $defaults = [
        'category' => '',
        'name'     => '',
        'uid'      => '',
        'role'     => 'user',
        'icon'     => 'fas fa-tasks',
        'summary'  => '',
    ];

    /**
     * Sets up common resources for Actions.
     */
    public function __construct(?Job $job = null, ?WorkflowsConfig $config = null, ?RequestInterface $request = null, ?ResponseInterface $response = null)
    {
        parent::__construct();

        $this->job      = $job;
        $this->config   = $config ?? config('Workflows');
        $this->request  = $request ?? service('request');
        $this->response = $response ?? service('response');

        $this->jobs = model($this->config->jobModel);

        $this->initialize();
    }

    //--------------------------------------------------------------------

    /**
     * Creates the database record for this class based on its definition.
     *
     * @throws RuntimeException for insert failures
     *
     * @return int The ID of the new/exsiting class
     */
    public function register(): int
    {
        $actions = model(ActionModel::class);

        // Check for an existing entry
        if ($action = $actions->where('uid', $this->attributes['uid'])->first()) {
            return $action->id;
        }

        $row          = $this->toArray();
        $row['class'] = static::class;

        return (int) $actions->insert($row);
    }

    /**
     * Deletes this action from the database (soft).
     *
     * @return bool Result from the model
     */
    public function remove(): bool
    {
        return model(ActionModel::class)->where('uid', $this->attributes['uid'])->delete();
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

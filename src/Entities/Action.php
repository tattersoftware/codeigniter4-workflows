<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity\Entity;
use Config\Services;
use Tatter\Users\Interfaces\HasPermission;
use Tatter\Workflows\BaseAction;
use Tatter\Workflows\Exceptions\WorkflowsException;

class Action extends Entity
{
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts = [
        'name' => 'string',
    ];

    /**
     * Default set of attributes.
     */
    protected $attributes = [
        'role' => '',
    ];

    /**
     * Cached Action instance for "class" attribute.
     *
     * @var BaseAction|null
     */
    private $instance;

    /**
     * Validates and runs the specified method from the instance.
     *
     * @throws WorkflowsException
     *
     * @return mixed Result of the instance method
     */
    public function __call(string $name, array $params)
    {
        // Make sure the instance supports the requested method
        $instance = $this->getInstance();
        if (! is_callable([$instance, $name])) {
            throw WorkflowsException::forUnsupportedActionMethod($this->attributes['name'], $name);
        }

        return $instance->{$name}(...$params);
    }

    /**
     * Gets the associated Action instance.
     */
    public function getInstance(): BaseAction
    {
        if ($this->instance === null) {
            $this->instance = new $this->attributes['class']();
        }

        return $this->instance;
    }

    /**
     * Formulate the current route for this Action, with optional job
     * E.g.: return redirect()->to(site_url($action->route));.
     *
     * @param int|string|null $jobId
     */
    public function getRoute($jobId = null): string
    {
        $route = '/' . config('Workflows')->routeBase . '/' . $this->attributes['uid'];

        if ($jobId !== null) {
            $route .= '/' . $jobId;
        }

        return $route;
    }

    /**
     * Checks if role filter is enabled and if a user
     * (defaults to current) may access this Action.
     */
    public function mayAccess(?HasPermission $user = null): bool
    {
        // Anyone can run user actions
        if ($this->attributes['role'] === '') {
            return true;
        }

        // If no user was provided then get the current user
        if (null === $user) {
            /** @var HasPermission|null $user */
            $user = Services::users()->findById(user_id());
        }

        // If still no user then deny
        if (null === $user) {
            return false;
        }

        return $user->hasPermission($this->attributes['role']);
    }
}

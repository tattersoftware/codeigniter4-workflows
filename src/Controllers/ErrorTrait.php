<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Workflows\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use Tatter\Workflows\Config\Workflows as WorkflowsConfig;
use Tatter\Workflows\Exceptions\WorkflowsException;

/**
 * Error Trait.
 *
 * Common error handling for
 * all module Controllers.
 *
 * @property ResponseInterface $response
 * @property WorkflowsConfig   $config
 */
trait ErrorTrait
{
    /**
     * Handles errors based on Config settings.
     *
     * @throws WorkflowsException
     *
     * @return ResponseInterface The error view, if silent mode
     */
    protected function handleError(WorkflowsException $exception): ResponseInterface
    {
        if (! $this->config->silent) {
            throw $exception;
        }

        $this->response->setBody(view($this->config->views['messages'], [
            'layout' => $this->config->layouts['public'],
            'error'  => $exception->getMessage(),
        ]));

        return $this->response;
    }
}

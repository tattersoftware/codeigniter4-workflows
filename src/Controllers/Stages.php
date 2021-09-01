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

use CodeIgniter\Controller;
use Tatter\Workflows\Models\StageModel;

class Stages extends Controller
{
    /**
     * Update a Stage (AJAX).
     *
     * @param mixed $stageId
     *
     * @return string
     */
    public function update($stageId)
    {
        if (! model(StageModel::class)->update($stageId, $this->request->getPost())) {
            echo 'Error: unable to update';
        }

        return '';
    }
}

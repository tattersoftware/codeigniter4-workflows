<?php

namespace Tatter\Workflows\Controllers;

use Tatter\Workflows\Models\StageModel;

class Stages extends BaseController
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

<?php

namespace Tatter\Workflows\Controllers;

use Tatter\Workflows\Models\StageModel;

class Stages extends BaseController
{
    /**
     * Update a Stage (AJAX).
     *
     * @param int|string $stageId
     */
    public function update($stageId): string
    {
        if (! model(StageModel::class)->update($stageId, $this->request->getPost())) {
            $message = implode(' ', model(StageModel::class)->errors());

            return 'Error: unable to update: ' . $message;
        }

        return '';
    }
}

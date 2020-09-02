<?php namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use Tatter\Workflows\Models\StageModel;

class Stages extends Controller
{
    /**
     * Update a Stage (AJAX)
     *
     * @return string
     */
	public function update($stageId)
	{
		if (! model(StageModel::class)->update($stageId, $this->request->getPost()))
		{
			echo 'Error: unable to update';
		}

		return '';
	}
}

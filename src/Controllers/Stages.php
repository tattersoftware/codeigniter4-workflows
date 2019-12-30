<?php namespace Tatter\Workflows\Controllers;

use CodeIgniter\Controller;
use Tatter\Workflows\Models\StageModel;

class Stages extends Controller
{
	// AJAX update a stage
	public function update($stageId)
	{
		if (! (new StageModel())->model->update($stageId, $this->request->getPost()))
		{
			echo 'Error: unable to update';
		}
	}
}

<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;

use Tatter\Workflows\Models\StageModel;

class WorkflowModel extends Model
{
	protected $table      = 'workflows';
	protected $primaryKey = 'id';

	protected $returnType = '\Tatter\Workflows\Entities\Workflow';
	protected $useSoftDeletes = true;

	protected $allowedFields = [
		'name', 'category', 'icon', 'summary', 'description'
	];

	protected $useTimestamps = true;

	protected $validationRules    = [
		'name'     => 'required|max_length[255]',
		'summary'  => 'required|max_length[255]',
	];
	protected $validationMessages = [];
	protected $skipValidation     = false;
	
	// batch load stages for a group of workflows
	public function fetchStages($workflows)
	{
		$stages = new StageModel();
		$workflowIds = array_column($workflows, 'id');
		
		$result = [];
		foreach ($stages->whereIn('workflow_id', $workflowIds)->orderBy('id', 'asc')->findAll() as $stage):
			if (! isset($result[$stage->workflow_id]))
				$result[$stage->workflow_id] = [];
			$result[$stage->workflow_id][] = $stage;
		endforeach;
		
		return $result;
	}
}

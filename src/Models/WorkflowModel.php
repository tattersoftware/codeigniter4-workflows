<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;
use Tatter\Workflows\Entities\Workflow;
use Tatter\Workflows\Models\StageModel;

class WorkflowModel extends Model
{
	use \Tatter\Audits\Traits\AuditsTrait;
	
	protected $table          = 'workflows';
	protected $returnType     = Workflow::class;
	protected $useSoftDeletes = true;
	protected $useTimestamps  = true;
	protected $allowedFields  = [
		'name', 'category', 'icon', 'summary', 'description'
	];

	protected $validationRules    = [
		'name'     => 'required|max_length[255]',
		'summary'  => 'required|max_length[255]',
	];

	/*** Tatter\Audits ***/
	protected $afterInsert = ['auditInsert'];
	protected $afterUpdate = ['auditUpdate'];
	protected $afterDelete = ['auditDelete'];
	
	// Batch load related stages for the given workflows
	public function fetchStages($workflows)
	{
		$result = [];

		$workflowIds = array_column($workflows, 'id');
		if (empty($workflowIds))
		{
			return $result;
		}

		foreach (model(StageModel::class)
			->whereIn('workflow_id', $workflowIds)
			->orderBy('id', 'asc')
			->findAll() as $stage)
		{
			if (! isset($result[$stage->workflow_id]))
			{
				$result[$stage->workflow_id] = [];
			}

			$result[$stage->workflow_id][] = $stage;
		}

		return $result;
	}
}

<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;

use Tatter\Workflows\Models\StageModel;

class WorkflowModel extends Model
{
	use \Tatter\Audits\Traits\AuditsTrait;
	
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
	
	/*** Tatter\Audits ***/
	protected $afterInsert = ['auditInsert'];
	protected $afterUpdate = ['auditUpdate'];
	protected $afterDelete = ['auditDelete'];
	
	// batch load stages for a group of workflows
	public function fetchStages($workflows)
	{
		$stages = new StageModel();
		$result = [];

		$workflowIds = array_column($workflows, 'id');
		if (empty($workflowIds))
			return $result;

		foreach ($stages->whereIn('workflow_id', $workflowIds)->orderBy('id', 'asc')->findAll() as $stage):
			if (! isset($result[$stage->workflow_id]))
				$result[$stage->workflow_id] = [];
			$result[$stage->workflow_id][] = $stage;
		endforeach;
		
		return $result;
	}
}

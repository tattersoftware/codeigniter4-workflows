<?php namespace Tatter\Workflows\Models;

use CodeIgniter\Model;
use Faker\Generator;
use Tatter\Users\Interfaces\HasPermission;
use Tatter\Workflows\Entities\Stage;
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
		'name', 'category', 'role', 'icon', 'summary', 'description',
	];

	protected $validationRules    = [
		'name'     => 'required|max_length[255]',
		'summary'  => 'required|max_length[255]',
	];

	/*** Tatter\Audits ***/
	protected $afterInsert = ['auditInsert'];
	protected $afterUpdate = ['auditUpdate'];
	protected $afterDelete = ['auditDelete'];
	
	/**
	 * Batch load related Stages for the
	 * supplied workflows.
	 *
	 * @param Workflow[] $workflows
	 *
	 * @return array<int,Stage[]> Stages indexed by their Workflow
	 */
	public function fetchStages(array $workflows)
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
			/** @var Stage $stage */
			if (! isset($result[$stage->workflow_id]))
			{
				$result[$stage->workflow_id] = [];
			}

			$result[$stage->workflow_id][] = $stage;
		}

		return $result;
	}
	
	/**
	 * Get Workflows allowed for a user.
	 *
	 * @param HasPermission $user
	 *
	 * @return Workflow[]
	 */
	public function getForUser(HasPermission $user)
	{
		// First load this user's explicit associations
		$explicits = [];
		foreach (model(ExplicitModel::class)->where('user_id', $user->getId())->findAll() as $explicit)
		{
			$explicits[$explicit->workflow_id] = (bool) $explicit->permitted;
		}

		// Cross check all Workflows
		$workflows = [];
		foreach ($this->findAll() as $workflow)
		{
			if ($workflow->mayAccess($user, $explicits))
			{
				$workflows[] = $workflow;
			}
		}

		return $workflows;
	}

	/**
	 * Faked data for Fabricator.
	 *
	 * @param Generator $faker
	 *
	 * @return Workflow
	 */
	public function fake(Generator &$faker): Workflow
	{
		return new Workflow([
			'name'        => $faker->word,
			'category'    => $faker->streetSuffix,
			'icon'        => $faker->safeColorName,
			'summary'     => $faker->sentence,
			'description' => $faker->paragraph,
		]);
	}
}

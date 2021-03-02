<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;
use Config\Services;
use Tatter\Users\Interfaces\HasPermission;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\ExplicitModel;
use Tatter\Workflows\Models\StageModel;
use RuntimeException;

class Workflow extends Entity
{
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	/**
	 * Default set of attributes
	 */
	protected $attributes = [
		'role' => '',
	];

	// Get this workflow's stages
	// Returns ordered stage objects with their IDs as keys
	public function getStages()
	{
		$stages = [];

		foreach (model(StageModel::class)
			->where('workflow_id', $this->attributes['id'])
			->orderBy('id', 'asc')
			->findAll() as $stage)
		{
			$stages[$stage->id] = $stage;
		}

		return $stages;
	}

	/**
	 * Checks if role filter is enabled and if a user
	 * (defaults to current) may access this Workflow.
	 *
	 * @param HasPermission|null $user
	 * @param array<int,bool>|null $explicits An array of explicit associations from
	 *                                        users_workflows. Mostly injected so when
	 *                                        checking many Workflows at once to prevent
	 *                                        duplicate database calls
	 *
	 * @return bool
	 */
	public function mayAccess(HasPermission $user = null, array $explicits = null): bool
	{
		// If no user was provided then try for the current user
		if (is_null($user) && $userId = user_id())
		{
			/** @var HasPermission|null $user */
			$user = Services::users()->findById($userId);
		}

		// Check explicits first
		if (is_null($explicits))
		{
			if ($user && $explicit = model(ExplicitModel::class)
				->where('user_id', $user->getId())
				->where('workflow_id', $this->attributes['id'])
				->first())
			{
				return (bool) $explicit->permitted;
			}
		}
		elseif (isset($explicits[$this->attributes['id']]))
		{
			return (bool) $explicits[$this->attributes['id']];
		}

		// Anyone else is allowed unrestricted Workflows
		if ($this->attributes['role'] === '' || $this->attributes['role'] === 'user')
		{
			return true;
		}

		// If still no user then deny
		if (is_null($user))
		{
			return false;
		}

		return $user->hasPermission($this->attributes['role']);
	}
}

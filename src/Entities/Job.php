<?php namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity;
use CodeIgniter\I18n\Time;
use Tatter\Workflows\Entities\Action;
use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Entities\Workflow;
use Tatter\Workflows\Exceptions\WorkflowsException;
use Tatter\Workflows\Models\JobflagModel;
use Tatter\Workflows\Models\JobModel;
use Tatter\Workflows\Models\StageModel;
use Tatter\Workflows\Models\ActionModel;
use Tatter\Workflows\Models\WorkflowModel;
use RuntimeException;

class Job extends Entity
{
	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
	];

	protected $casts = [
		'workflow_id' => 'int',
		'stage_id'    => '?int',
	];

	/**
	 * Stored entity for the current Stage. Can be null for completed Jobs.
	 *
	 * @var Stage|null
	 */
	protected $stage;

	/**
	 * Indicates whether the Stage has been loaded.
	 *
	 * @var boolean
	 */
	protected $stageFlag = false;

	/**
	 * Stored entity for the Workflow.
	 *
	 * @var Workflow
	 */
	protected $workflow;

	/**
	 * Stored flags from `jobflags`.
	 *
	 * @var array<string,Time>|null
	 */
	protected $flags;

	/**
	 * Verifies the primary key to prevent operations on
	 * un-created database entries.
	 *
	 * @return $this
	 *
	 * @throws RuntimeException
	 */
	protected function ensureCreated(): self
	{
		if (empty($this->attributes['id']))
		{
			throw new RuntimeException('Job must be created first.');
		}

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Fetches, stores, and returns all this job's flags (from `jobflags`)
	 *
	 * @return array<string,Time>
	 */
	public function getFlags(): array
	{
		$this->ensureCreated();

		if (is_null($this->flags))
		{
			$this->flags = [];

			foreach (model(JobflagModel::class)->where('job_id', $this->attributes['id'])->findAll() as $flag)
			{
				$this->flags[$flag->name] = new Time($flag->created_at);
			}
		}

		return $this->flags;
	}

	/**
	 * Gets a flag by its name
	 *
	 * @return Time|null
	 */
	public function getFlag(string $name): ?Time
	{
		return $this->getFlags()[$name] ?? null;
	}

	/**
	 * Creates a flag for the given name
	 *
	 * @return $this
	 */
	public function setFlag(string $name): self
	{
		if (isset($this->getFlags()[$name]))
		{
			model(JobflagModel::class)
				->where('job_id', $this->attributes['id'])
				->where('name', $name)
				->update(null, ['created_at' => date('Y-m-d H:i:s'),
			]);
		}
		else
		{
			$this->flags[$name] = new Time('now');

			model(JobflagModel::class)->insert([
				'job_id' => $this->attributes['id'],
				'name'   => $name,
			]);
		}

		return $this;
	}

	/**
	 * Removes a flag for the given name
	 *
	 * @return $this
	 */
	public function clearFlag(string $name): self
	{
		$this->ensureCreated();

		model(JobflagModel::class)
			->where('job_id', $this->attributes['id'])
			->where('name', $name)
			->delete();

		if (is_array($this->flags) && isset($this->flags[$name]))
		{
			unset($this->flags[$name]);
		}

		return $this;
	}

	/**
	 * Removes all flags
	 *
	 * @return $this
	 */
	public function clearFlags(): self
	{
		$this->ensureCreated();

		model(JobflagModel::class)
			->where('job_id', $this->attributes['id'])
			->delete();

		$this->flags = [];

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Gets the current Stage
	 *
	 * @return Stage|null
	 */
	public function getStage(): ?Stage
	{
		if (! $this->stageFlag)
		{
			$this->stageFlag = true;

			$this->stage = isset($this->attributes['stage_id'])
				? model(StageModel::class)->find($this->attributes['stage_id'])
				: null;
		}

		return $this->stage;
	}

	/**
	 * Gets the Workflow
	 *
	 * @return Workflow
	 */
	public function getWorkflow(): Workflow
	{
		if ($this->workflow === null)
		{
			$this->workflow = model(WorkflowModel::class)->find($this->attributes['workflow_id']);
		}

		return $this->workflow;
	}

	/**
	 * Gets all Stages from the Workflow
	 *
	 * @return array<Stage>
	 */
	public function getStages(): array
	{
		return $this->getWorkflow()->stages;
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the next Stage
	 *
	 * @return Stage|null
	 */
	public function next(): ?Stage
	{
		return $this->_next($this->getStages());
	}

	/**
	 * Returns the previous Stage
	 *
	 * @return Stage|null
	 */
	public function previous(): ?Stage
	{
		// Look through all the Stages backwards
		return $this->_next(array_reverse($this->getStages()));
	}

	/**
	 * Returns the next Stage from an array of Stages
	 *
	 * @param array<Stage> $stages
	 *
	 * @return Stage|null
	 */
	protected function _next($stages): ?Stage
	{
		// look through the stages
		$stage = current($stages);
		do
		{
			// Check if this is the current stage
			if ($stage->id === $this->attributes['stage_id'])
			{
				// Look for the next Stage
				if (! $stage = next($stages))
				{
					return null;
				}

				return $stage;
			}
		}
		while ($stage = next($stages));

		return null;
	}

	//--------------------------------------------------------------------

	/**
	 * Moves through the Workflow, skipping non-required Stages but running their Action functions.
	 *
	 * @param integer $actionId ID of the target Action
	 * @param bool $checkRequired Whether to check for required stages while traveling
	 *
	 * @return array  Array of boolean results from each Action's up/down method
	 * @throws WorkflowsException
	 */
	public function travel(int $actionId, bool $checkRequired = true): array
	{
		$this->ensureCreated();

		// Make sure the target Stage exists
		if (! $target = model(StageModel::class)
			->where('action_id', $actionId)
			->where('workflow_id', $this->attributes['workflow_id'])
			->first())
		{
			throw WorkflowsException::forStageNotFound();
		}

		// Get the Workflow, Stages, and current Stage
		$workflow = $this->getWorkflow();
		$stages   = $this->getStages();
		$current  = $this->getStage();

		// Determine direction of travel
		if ($current->id < $target->id)
		{
			if ($checkRequired)
			{
				// Make sure this won't skip any required stages
				$test = model(StageModel::class)
					->where('id >=', $current->id)
					->where('id <', $target->id)
					->where('workflow_id', $this->attributes['workflow_id'])
					->where('required', 1)
					->first();

				if (! empty($test))
				{
					throw WorkflowsException::forSkipRequiredStage($test->name);
				}
			}

			$method = 'up';
		}
		elseif ($current->id > $target->id)
		{
			$method = 'down';
			arsort($stages);
		}
		// Already there!
		else
		{
			return [];
		}

		// Travel the Workflow running the appropriate method
		$results = [];
		foreach ($stages as $stage)
		{
			// Check if we need to run this action
			if (($method === 'up'   && $stage->id > $current->id) ||
				($method === 'down' && $stage->id <= $current->id)
			)
			{
				$results[$stage->id] = $stage->action->setJob($this)->$method();
			}

			// If the target was reached then we're done
			if ($stage->id === $target->id)
			{
				break;
			}
		}

		// Update the Job
		model(JobModel::class)->update($this->attributes['id'], ['stage_id' => $target->id]);
		$this->attributes['stage_id'] = $target->id;

		return $results;
	}
}

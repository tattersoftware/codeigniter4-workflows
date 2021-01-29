<?php namespace Tatter\Workflows\Entities;

use Tatter\Workflows\Entities\Stage;
use Tatter\Workflows\Models\StageModel;

class Joblog extends \CodeIgniter\Entity
{
	protected $dates = ['created_at'];

	/**
	 * Cached entity for the "from" Stage
	 *
	 * @var Stage
	 */
	protected $from;

	/**
	 * Cached entity for the "to" Stage
	 *
	 * @var Stage
	 */
	protected $to;

	/**
	 * Cached result for user lookup
	 *
	 * @var array|object|null
	 */
	private $user;

	/**
	 * Loads (if necessary) and returns the stage this logs the job changing from.
	 *
	 * @return Stage|null  Stage the job moved from
	 */
	public function getFrom(): ?Stage
	{
		if ($this->from === null && $this->attributes['stage_from'])
		{
			$this->from = (new StageModel())->find($this->attributes['stage_from']);
		}

		return $this->from;
	}

	/**
	 * Sets the "from" stage - mostly used by the model to seed entities
	 *
	 * @param Stage|null $stage Stage the job moved to
	 */
	public function setFrom(Stage $stage = null)
	{
		$this->from = $stage;
	}

	/**
	 * Loads (if necessary) and returns the stage this logs the job changing to.
	 *
	 * @return Stage|null $stage  Stage the job moved from
	 */
	public function getTo(): ?Stage
	{
		if ($this->to === null && $this->attributes['stage_to'])
		{
			$this->to = (new StageModel())->find($this->attributes['stage_to']);
		}

		return $this->to;
	}

	/**
	 * Sets the "to" stage - mostly used by the model to seed entities
	 *
	 * @param Stage|null $stage Stage the job moved from
	 */
	public function setTo(Stage $stage = null)
	{
		$this->to = $stage;
	}

	/**
	 * Attempts to locate a user corresponding to user_id.
	 * Relies on locating a UserModel class via Factories.
	 *
	 * @return array|object|null
	 */
	public function getUser()
	{
		if (empty($this->attributes['user_id']))
		{
			return null;
		}

		if (is_null($this->user))
		{
			// Try to locate a Model
			if ($model = model('UserModel'))
			{
				$this->user = $model->find($this->attributes['user_id']);
			}
		}

		return $this->user;
	}
}

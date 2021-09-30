<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity\Entity;
use Config\Services;
use Tatter\Users\UserEntity;
use Tatter\Workflows\Models\StageModel;

class Joblog extends Entity
{
    protected $dates = ['created_at'];

    protected $casts = [
        'job_id'     => 'int',
        'user_id'    => '?int',
        'stage_from' => '?int',
        'stage_to'   => '?int',
    ];

    /**
     * Cached entity for the "from" Stage.
     *
     * @var Stage
     */
    private $from;

    /**
     * Cached entity for the "to" Stage.
     *
     * @var Stage
     */
    private $to;

    /**
     * Cached result for user lookup.
     *
     * @var array|object|null
     */
    private $user;

    /**
     * Loads (if necessary) and returns the stage this logs the job changing from.
     *
     * @return Stage|null Stage the job moved from
     */
    public function getFrom(): ?Stage
    {
        if ($this->from === null && $this->attributes['stage_from']) {
            $this->from = model(StageModel::class)->find($this->attributes['stage_from']);
        }

        return $this->from;
    }

    /**
     * Sets the "from" stage - mostly used by the model to seed entities.
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
        if ($this->to === null && $this->attributes['stage_to']) {
            $this->to = model(StageModel::class)->find($this->attributes['stage_to']);
        }

        return $this->to;
    }

    /**
     * Sets the "to" stage - mostly used by the model to seed entities.
     *
     * @param Stage|null $stage Stage the job moved from
     */
    public function setTo(Stage $stage = null)
    {
        $this->to = $stage;
    }

    /**
     * Returns the UserEntity corresponding to user_id.
     *
     * @return UserEntity|null
     */
    public function getUser(): ?UserEntity
    {
        if (empty($this->attributes['user_id'])) {
            return null;
        }

        if (is_null($this->user)) {
            $this->user = Services::users()->findById($this->attributes['user_id']);
        }

        return $this->user;
    }
}

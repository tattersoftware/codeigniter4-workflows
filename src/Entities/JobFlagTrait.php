<?php

namespace Tatter\Workflows\Entities;

use CodeIgniter\I18n\Time;
use Tatter\Workflows\Models\JobflagModel;

/**
 * @mixin Job
 */
trait JobFlagTrait
{
    /**
     * Store of jobflags
     *
     * @var array<string,Time>|null
     */
    protected ?array $flags = null;

    /**
     * Fetches, stores, and returns all this job's flags
     *
     * @return array<string,Time>
     */
    public function getFlags(): array
    {
        $this->ensureCreated();

        if (null === $this->flags) {
            $this->flags = [];

            foreach (model(JobflagModel::class)->where('job_id', $this->attributes['id'])->findAll() as $flag) {
                $this->flags[$flag->name] = new Time($flag->created_at);
            }
        }

        return $this->flags;
    }

    /**
     * Gets a flag by its name.
     */
    public function getFlag(string $name): ?Time
    {
        return $this->getFlags()[$name] ?? null;
    }

    /**
     * Creates a flag for the given name.
     *
     * @return $this
     */
    public function setFlag(string $name): self
    {
        if (isset($this->getFlags()[$name])) {
            model(JobflagModel::class)
                ->where('job_id', $this->attributes['id'])
                ->where('name', $name)
                ->update(null, ['created_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            $this->flags[$name] = new Time('now');

            model(JobflagModel::class)->insert([
                'job_id' => $this->attributes['id'],
                'name'   => $name,
            ]);
        }

        return $this;
    }

    /**
     * Removes a flag for the given name.
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

        if (is_array($this->flags) && isset($this->flags[$name])) {
            unset($this->flags[$name]);
        }

        return $this;
    }

    /**
     * Removes all flags.
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
}

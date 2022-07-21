<?php

namespace Tatter\Workflows\Entities;

use CodeIgniter\Entity\Entity;
use RuntimeException;

abstract class BaseEntity extends Entity
{
    /**
     * Verifies the primary key to prevent operations on
     * nonexistant database entries.
     *
     * @throws RuntimeException
     *
     * @return $this
     */
    final protected function ensureCreated(): self
    {
        if (empty($this->attributes['id'])) {
            throw new RuntimeException(static::class . ' must exist in the database.');
        }

        return $this;
    }
}

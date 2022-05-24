<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Workflows\Models;

use CodeIgniter\Model;
use CodeIgniter\Test\Fabricator;
use Faker\Generator;
use Tatter\Audits\Traits\AuditsTrait;

class ExplicitModel extends Model
{
    use AuditsTrait;

    protected $table          = 'users_workflows';
    protected $returnType     = 'object';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = true;
    protected $updatedField   = '';
    protected $allowedFields  = [
        'user_id', 'workflow_id', 'permitted',
    ];
    protected $validationRules = [
        'user_id'     => 'required|is_natural_no_zero',
        'workflow_id' => 'required|is_natural_no_zero',
        'permitted'   => 'required',
    ];

    // Tatter\Audits
    protected $afterInsert = ['auditInsert'];
    protected $afterUpdate = ['auditUpdate'];
    protected $afterDelete = ['auditDelete'];

    /**
     * Faked data for Fabricator.
     */
    public function fake(Generator &$faker): object
    {
        return (object) [
            'user_id'     => random_int(1, Fabricator::getCount('users') ?: 10),
            'workflow_id' => random_int(1, Fabricator::getCount('workflows') ?: 4),
            'permitted'   => (bool) random_int(0, 4),
        ];
    }
}

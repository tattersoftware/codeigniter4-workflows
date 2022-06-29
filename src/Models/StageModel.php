<?php

namespace Tatter\Workflows\Models;

use CodeIgniter\Model;
use CodeIgniter\Test\Fabricator;
use Faker\Generator;
use Tatter\Audits\Traits\AuditsTrait;
use Tatter\Workflows\Entities\Stage;

class StageModel extends Model
{
    use AuditsTrait;

    protected $table         = 'stages';
    protected $returnType    = Stage::class;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'workflow_id',
        'action_id',
        'input',
        'required',
    ];
    protected $validationRules = [
        'workflow_id' => 'required|is_natural_no_zero',
        'action_id'   => 'required|string',
        'input'       => 'permit_empty|max_length[63]',
    ];

    // Tatter\Audits
    protected $afterInsert = ['auditInsert'];
    protected $afterUpdate = ['auditUpdate'];
    protected $afterDelete = ['auditDelete'];

    /**
     * Faked data for Fabricator.
     */
    public function fake(Generator &$faker): Stage
    {
        return new Stage([
            'workflow_id' => random_int(1, Fabricator::getCount('workflows') ?: 4),
            'action_id'   => 'info',
            'required'    => random_int(0, 5) ? 1 : 0,
        ]);
    }
}

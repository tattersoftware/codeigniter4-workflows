<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Workflows\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Tatter\Workflows\Factories\ActionFactory;
use Tatter\Workflows\Models\ActionModel;

class ActionsRegister extends BaseCommand
{
    protected $group       = 'Workflows';
    protected $name        = 'actions:list';
    protected $description = 'Locate, register, and display workflow actions';
    protected $usage       = 'actions:register';
    protected $arguments   = [];

    public function run(array $params = []): void
    {
        ActionFactory::register();

        $actions = model(ActionModel::class);

        // get all actions
        $rows = $actions
            ->builder()
            ->select('id, name, category, uid, role, class, summary')
            ->orderBy('name', 'asc')
            ->get()->getResultArray();

        if (empty($rows)) {
            CLI::write('There are no registered actions.', 'yellow');
        } else {
            $thead = [
                'Action ID',
                'Name',
                'Category',
                'UID',
                'Role',
                'Class',
                'Summary',
            ];
            CLI::table($rows, $thead);
        }
    }
}

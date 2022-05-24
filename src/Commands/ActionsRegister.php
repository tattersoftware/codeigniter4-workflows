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
use Tatter\Workflows\Registrar;

class ActionsRegister extends BaseCommand
{
    protected $group       = 'Workflows';
    protected $name        = 'actions:register';
    protected $description = 'Search for new actions and add them to the database';
    protected $usage       = 'actions:register';
    protected $arguments   = [];

    public function run(array $params = [])
    {
        $count = Registrar::actions();

        if ($count === 0) {
            CLI::write('No Actions found in any namespace.', 'yellow');

            return;
        }

        $this->call('actions:list');
    }
}

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

class ActionsList extends BaseCommand
{
    protected $group       = 'Workflows';
    protected $name        = 'actions:list';
    protected $description = 'Display available workflow actions';
    protected $usage       = 'actions:list';

    public function run(array $params = []): void
    {
        $actions = [];

        foreach (ActionFactory::getAllAttributes() as $attributes) {
            $actions[] = array_intersect_key($attributes, array_flip([
                'id',
                'name',
                'role',
                'icon',
                'category',
                'summary',
                'class',
            ]));
        }

        if ($actions === []) {
            CLI::write('There are no available actions.', 'yellow');
        } else {
            $thead = [
                'Action ID',
                'Name',
                'Role',
                'Icon',
                'Category',
                'Summary',
                'Class',
            ];
            CLI::table($actions, $thead);
        }
    }
}

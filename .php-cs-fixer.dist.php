<?php

/**
 * This file is part of Tatter Workflows.
 *
 * (c) 2019 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Nexus\CsConfig\Factory;
use PhpCsFixer\Finder;
use Utils\PhpCsFixer\CodeIgniter4;

$finder = Finder::create()
    ->files()
    ->in(__DIR__)
    ->append([__FILE__]);

$overrides = [];

$options = [
    'finder' => $finder,
];

return Factory::create(new CodeIgniter4(), $overrides, $options)->forLibrary(
    'Tatter Workflows',
    'Tatter Software',
    '',
    2019
);

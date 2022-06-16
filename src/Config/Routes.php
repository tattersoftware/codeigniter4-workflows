<?php

namespace Tatter\Workflows\Config;

$routes ??= service('routes');

$config  = config('Workflows');
$options = [
    'filter'    => 'assets:\Tatter\Workflows\Bundles\WorkflowsBundle',
    'namespace' => '\Tatter\Workflows\Controllers',
];

$routes->group($config->routeBase, $options, static function ($routes) {
    // Jobs routes
    $routes->get('show/(:num)', 'Jobs::show/$1');
    $routes->get('new', 'Jobs::new');
    $routes->get('new/(:num)', 'Jobs::new/$1');
    $routes->get('(:num)/delete', 'Jobs::delete/$1');
    $routes->post('(:num)/delete', 'Jobs::delete/$1');

    // Runner route
    $routes->get('(:num)', 'Runner::resume/$1');
    $routes->post('(:num)', 'Runner::resume/$1');
    $routes->add('(.+)', 'Runner::run/$1');
});

$routes->resource('actions', [
    'controller' => '\Tatter\Workflows\Controllers\Actions',
    'filter'     => 'assets:\Tatter\Workflows\Bundles\WorkflowsBundle',
    'websafe'    => 1,
]);
$routes->resource('stages', [
    'controller' => '\Tatter\Workflows\Controllers\Stages',
    'filter'     => 'assets:\Tatter\Workflows\Bundles\WorkflowsBundle',
    'websafe'    => 1,
]);
$routes->resource('workflows', [
    'controller' => '\Tatter\Workflows\Controllers\Workflows',
    'filter'     => 'assets:\Tatter\Workflows\Bundles\WorkflowsBundle',
    'websafe'    => 1,
]);

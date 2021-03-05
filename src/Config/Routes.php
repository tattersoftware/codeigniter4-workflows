<?php
$routes = $routes ?? service('routes');
$config = config('Workflows');

// Runner routes
$routes->get( $config->routeBase . '/show/(:num)',   '\Tatter\Workflows\Controllers\Runner::show/$1');
$routes->get( $config->routeBase . '/new',     '\Tatter\Workflows\Controllers\Runner::new');
$routes->get( $config->routeBase . '/new/(:num)',     '\Tatter\Workflows\Controllers\Runner::new/$1');
$routes->get( $config->routeBase . '/(:num)/delete', '\Tatter\Workflows\Controllers\Runner::delete/$1');
$routes->post($config->routeBase . '/(:num)/delete', '\Tatter\Workflows\Controllers\Runner::delete/$1');
$routes->add( $config->routeBase . '/(.+)',          '\Tatter\Workflows\Controllers\Runner::run/$1');

// Admin dashboard routes
$routes->resource('stages',    ['websafe' => 1, 'controller'=>'\Tatter\Workflows\Controllers\Stages']);
$routes->resource('actions',   ['websafe' => 1, 'controller'=>'\Tatter\Workflows\Controllers\Actions']);
$routes->resource('workflows', ['websafe' => 1, 'controller'=>'\Tatter\Workflows\Controllers\Workflows']);

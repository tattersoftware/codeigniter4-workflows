<?php
$config = config('Workflows');

// Runner routes
$routes->get( $config->routeBase . '/show/(:num)',   '\Tatter\Workflows\Controllers\Runner::show/$1');
$routes->get( $config->routeBase . '/new(:any)',     '\Tatter\Workflows\Controllers\Runner::new/$1');
$routes->get( $config->routeBase . '/(:num)/delete', '\Tatter\Workflows\Controllers\Runner::delete/$1');
$routes->post($config->routeBase . '/(:num)/delete', '\Tatter\Workflows\Controllers\Runner::delete/$1');
$routes->add( $config->routeBase . '/(.+)',          '\Tatter\Workflows\Controllers\Runner::run/$1');

// Admin dashboard routes
$routes->resource('stages',    ['websafe' => 1, 'controller'=>'\Tatter\Workflows\Controllers\Stages']);
$routes->resource('tasks',     ['websafe' => 1, 'controller'=>'\Tatter\Workflows\Controllers\Tasks']);
$routes->resource('workflows', ['websafe' => 1, 'controller'=>'\Tatter\Workflows\Controllers\Workflows']);

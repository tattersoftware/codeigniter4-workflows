<?php

$config = class_exists('\Config\Workflows') ? new \Config\Workflows() : new \Tatter\Workflows\Config\Workflows();

// Runner routes
$routes->get($config->routeBase . '/new/(:num)',  'Tatter\Workflows\Controllers\Runner::new/$1');
$routes->add($config->routeBase . '/(.+)',  'Tatter\Workflows\Controllers\Runner::run/$1');

// Admin dashboard routes
$routes->resource('stages', ['controller' =>'Tatter\Workflows\Controllers\Stages']);
$routes->resource('tasks', ['controller' =>'Tatter\Workflows\Controllers\Tasks']);
$routes->resource('workflows', ['controller' =>'Tatter\Workflows\Controllers\Workflows']);

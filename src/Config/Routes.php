<?php

$config = class_exists('\Config\Workflows') ? new \Config\Workflows() : new \Tatter\Workflows\Config\Workflows();

$routes->add($config->routeBase . '/(.+)',  'Tatter\Workflows\Controllers\WorkflowRunner::run/$1');
$routes->resource('tasks', ['controller' =>'Tatter\Workflows\Controllers\Workflows']);
$routes->resource('workflows', ['controller' =>'Tatter\Workflows\Controllers\Workflows']);

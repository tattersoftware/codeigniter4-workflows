<?php

$config = class_exists('\Config\Workflows') ? new \Config\Workflows() : new \Tatter\Workflows\Config\Workflows();

$routes->add($config->routeBase . '/(.+)',  'Tatter\Workflows\Controllers\WorkflowRunner::run/$1');
$routes->resource('stages', ['controller' =>'Tatter\Workflows\Controllers\Stages']);
$routes->resource('workflows', ['controller' =>'Tatter\Workflows\Controllers\Workflows']);

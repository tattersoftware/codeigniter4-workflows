<?php

$routes->resource('workflows', ['controller' =>'Tatter\Workflows\Controllers\Workflows']);

$routes->get('tasks/(.+)',  'Tatter\Workflows\Controllers\Tasks::get/$1');
$routes->post('tasks/(.+)', 'Tatter\Workflows\Controllers\Tasks::post/$1');

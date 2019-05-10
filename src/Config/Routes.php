<?php

$routes->resource('workflows', ['controller' =>'Tatter\Workflows\Controllers\Workflows']);
$routes->add('jobs/(.+)',  'Tatter\Workflows\Controllers\Runner::run/$1');

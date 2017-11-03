<?php

use Router\Router;

define('CONTROLLERS', "Daxia\\Controllers\\");

Router::get('hi', function() {
    echo "welcome daxia";
});

Router::get('/', CONTROLLERS . 'Demo@index');
Router::get('page', CONTROLLERS . 'Demo@page');

Router::dispatch();

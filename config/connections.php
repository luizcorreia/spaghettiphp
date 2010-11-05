<?php

Connection::add(array(
    'development' => array(
        'driver' => 'MySql',
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'database' => 'rede_radios',
        'prefix' => ''
    ),
    'production' => array(
        'driver' => 'MySql',
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'database' => 'spaghetti',
        'prefix' => ''
    ),
    'test' => array(
        'driver' => 'MySql',
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'database' => 'spaghetti',
        'prefix' => ''
    )
));

$env = Config::read('App.environment');
Connection::add('default', Connection::config($env));

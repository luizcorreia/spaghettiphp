<?php

Connection::add(array(
    'development' => array(
        'driver' => 'MySql',
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => '',
        'database' => 'spaghetti',
        'prefix' => ''
    ),
    'production' => array(
        'driver' => 'MySql',
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => '',
        'database' => 'spaghetti',
        'prefix' => ''
    ),
    'test' => array(
        'driver' => 'MySql',
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => '',
        'database' => 'spaghetti',
        'prefix' => ''
    )
));

$env = Config::read('App.environment');
Connection::add('default', Connection::getConfig($env));

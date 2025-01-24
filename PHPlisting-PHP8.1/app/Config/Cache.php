<?php

return [
    'driver_default' => 'db',
    'drivers_supported' => ['collection', 'file', 'db', 'memcached'],

    'file' => [
        'path' => ROOT_PATH_PROTECTED . DS . 'Cache',
    ],

    'db' => [
        'table' => 'cache',
    ],
    
    'memcached' => [
        'host' => '127.0.0.1',
        'port' => '11211',
    ],
];

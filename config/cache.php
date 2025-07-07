<?php

return [
    'default' => 'file',

    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => __DIR__ . '/../storage/framework/cache',
        ],
    ],

    'ttl' => 3600, // 1 hour
];

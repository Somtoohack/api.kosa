<?php

return [
    'domain' => null,
    'path' => 'horizon',

    // 'middleware' => ['web'],
    'middleware' => ['api', 'horizon.auth'], // or your preferred auth middleware

    'use' => 'default',

    'environments' => [
        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default', 'emails', 'notifications'],
                'balance' => 'simple',
                'processes' => 10,
                'tries' => 3,
                'timeout' => 60,
            ],
        ],

        'local' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default', 'emails', 'notifications'],
                'balance' => 'simple',
                'processes' => 3,
                'tries' => 3,
            ],
        ],
    ],

    'metrics' => [
        'trim_snapshots' => [
            'job' => 24,
            'queue' => 24,
        ],
    ],

    'memory_limit' => 64,

    'defaults' => [
        'supervisor-1' => [
            'connection' => 'redis',
            'queue' => ['default'],
            'balance' => 'auto',
            'maxProcesses' => 1,
            'maxTime' => 0,
            'maxJobs' => 0,
            'memory' => 128,
            'tries' => 3,
            'timeout' => 60,
            'nice' => 0,
        ],
    ],

    'waits' => [
        'redis:default' => 60,
    ],
];

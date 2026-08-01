<?php

return [
    'environments' => [
        'production' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default', 'emails', 'notifications'],
                'balance' => 'auto',
                'autoScalingStrategy' => 'time',
                'maxProcesses' => 16,
                'maxTime' => 3600,
                'maxJobs' => 1000,
                'memory' => 512,
                'tries' => 3,
                'timeout' => 60,
                'nice' => 0,
                'backoff' => 10,
            ],
        ],

        'local' => [
            'supervisor-1' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'simple',
                'maxProcesses' => 3,
                'maxTime' => 3600,
                'maxJobs' => 1000,
                'memory' => 256,
                'tries' => 3,
                'timeout' => 60,
                'nice' => 0,
            ],
        ],
    ],
];

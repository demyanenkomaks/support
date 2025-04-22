<?php

return [
    'validate' => [
        'format' => [
            'phone' => 'regex:/^\+7\d{10}$/',
            'phone_international' => '/regex:^\+\d{7,15}$/',

            'date' => 'date_format:Y-m-d',
            'time' => 'date_format:H:i:s',
            'datetime' => 'date_format:Y-m-d H:i:s',
        ],
    ],

    'return' => [
        'format' => [
            'date' => 'Y-m-d',
            'time' => 'H:i:s',
            'datetime' => 'Y-m-d\TH:i:s\Z',
        ],
    ],
];

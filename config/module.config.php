<?php

return [
    'controllers'  => [
        'invokables' => [
        ],
    ],
    'router'       => [
        'routes' => [
        ],
    ],
    'view_manager' => [
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    'lockstate'    => [
        'base_url'   => 'https://api.lockstate.com',
        'auth_url'   => 'https://connect.lockstate.com',
        'app_id'     => 'YOUR_APP_ID_HERE',
        'app_secret' => 'YOUR_APP_SECRET_HERE',
    ],
];


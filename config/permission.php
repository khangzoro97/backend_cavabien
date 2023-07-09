<?php

use App\Models\Admin;
use App\Models\User;

return [
    [
        'admin' => [
            'home' => [User::ROLE_START_UP, User::ROLE_INVESTOR],
        ],
        'user' => [
            'dashboard' => [Admin::ROLE_ADMIN],
        ]
    ]
];

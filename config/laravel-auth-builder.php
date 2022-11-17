<?php


return [
    'guard' => 'web',
    'otp' => false,
    'model' => 'App\Models\User',
    'login_by' => 'email',
    'login_type' => 'email',
    'validation' => [
        'create' => [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|confirmed|min:6|max:191',
        ],
        'update' => [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'password' => 'sometimes|confirmed|min:6|max:191',
        ],
    ],

];

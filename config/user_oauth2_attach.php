<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table name
    |--------------------------------------------------------------------------
    */
    'table' => 'user_oauth2',

    /*
    |--------------------------------------------------------------------------
    | Model
    |--------------------------------------------------------------------------
    | Model (default): \ItDevgroup\LaravelUserOAuth2Attach\Model\UserOAuth2::class
    | Change to your custom class if you need to extend the model or change the table name
    | A custom class for user OAuth2 must inherit the base class \ItDevgroup\LaravelUserOAuth2Attach\Model\UserOAuth2
    */
    'model' => \ItDevgroup\LaravelUserOAuth2Attach\Model\UserOAuth2::class,

    /*
    |--------------------------------------------------------------------------
    | User model
    |--------------------------------------------------------------------------
    | Example: [
    |    'class' => \App\User:class,
    |    'email_field_name' => 'email',
    | ]
    */
    'user_model' => [
        'class' => null,
        'field_email' => 'email',
    ],

    /*
    |--------------------------------------------------------------------------
    | List of services
    |--------------------------------------------------------------------------
    | Example: [
    |    'service_name' => [
    |        'class' => \App\CustomService::class,
    |        'enabled' => true,
    |        'redirect_url' => 'http://...', // link to UI
    |        'params' => [
    |            'param_name' => 'param value',
    |        ]
    |    ],
    |    ...
    | ]
    |
    | params - custom variables for each service, for example, service keys,
    |          application id, etc., each service has its own sets
    */
    'services' => [
        'google' => [
            'class' => \ItDevgroup\LaravelUserOAuth2Attach\Services\GoogleService::class,
            'enabled' => true,
            'redirect_url' => 'http://localhost/google/redirect-url',
            'params' => [
                'credential_file' => resource_path('client_secret.json'),
                'access_type' => 'offline',
                'prompt' => 'consent',
                'include_granted_scopes' => true,
            ]
        ],
        'facebook' => [
            'class' => \ItDevgroup\LaravelUserOAuth2Attach\Services\FacebookService::class,
            'enabled' => true,
            'redirect_url' => 'http://localhost/facebook/redirect-url',
            'params' => [
                'app_id' => '',
                'app_secret' => '',
                'version' => 'v9.0',
            ]
        ],
    ]
];

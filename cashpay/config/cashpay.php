<?php

return [
    'floosak' => [
        'url' => env('FLOOSAK_URL'),
        'token' => env('FLOOSAK_TOKEN'),
        'phone' => env('FLOOSAK_PHONE'),
        'short_code' => env('FLOOSAK_SHORT_CODE'),
    ], 

    'cash' => [
        'url' => env('CASH_URL'),
        'username' => env('TAMKEEN_USERNAME'),
        'password' => env('TAMKEEN_PASSWORD'),
        'service_provider_id' => env('TAMKEEN_SERVICE_PROVIDER_ID'),
        'encryption_key' => env('TAMKEEN_ENCRYPTION_KEY'),
        'certificate_path' => env('TAMKEEN_CERTIFICATE_PATH'),
        'certificate_password' => env('TAMKEEN_CERTIFICATE_PASSWORD'),
    ],

    'jawali' => [
        'oauth_url' => env('JAWALI_OAUTH_URL'), 
        'oauth_password' => env('JAWALI_OAUTH_PASSWORD'),
        'org_id' => env('JAWALI_ORG_ID'),
        'user_name' => env('JAWALI_USER_NAME'),
        'wallet_url' => env('JAWALI_WALLET_URL'),
        'agent_wallet' => env('JAWALI_AGENT_WALLET'),
        'wallet_password' => env('JAWALI_WALLET_PASSWORD'),
        'refresh_token' => env('JAWALI_REFRESH_TOKEN'), 
    ],
];

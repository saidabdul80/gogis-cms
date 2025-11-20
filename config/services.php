<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'giras' => [
        'base_url' => env('GIRAS_BASE_URL', 'https://giras-backend.staging.irs.gm.gov.ng'),
        'client_id' => env('GIRAS_CLIENT_ID', '15'),
        'secret_key' => env('GIRAS_SECRET_KEY', ''),

        // Revenue Sub Head Configuration
        // Format: 'key' => revenue_sub_head_id
        // Add more revenue sub heads as needed in the future
        'revenue_sub_heads' => [
            'default' => env('GIRAS_DEFAULT_REVENUE_SUB_HEAD', 1052),
            'property_tax' => env('GIRAS_PROPERTY_TAX_REVENUE_SUB_HEAD', 1052),
            // Future revenue sub heads can be added here:
            // 'land_use_charge' => env('GIRAS_LAND_USE_CHARGE_REVENUE_SUB_HEAD', null),
            // 'development_levy' => env('GIRAS_DEVELOPMENT_LEVY_REVENUE_SUB_HEAD', null),
        ],

        // Default payment gateway
        'default_gateway' => env('GIRAS_DEFAULT_GATEWAY', 'paystack'),

        // Available payment gateways
        'gateways' => ['paystack', 'remita', 'interswitch'],
    ],

];

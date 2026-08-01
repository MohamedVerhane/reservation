<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment provider used for transactions
    | when no specific provider is specified.
    |
    */

    'default' => env('PAYMENT_DEFAULT_PROVIDER', 'stripe'),

    /*
    |--------------------------------------------------------------------------
    | Payment Currency
    |--------------------------------------------------------------------------
    |
    | The default currency for payment transactions. Can be overridden per
    | request using ChargeRequest.
    |
    */

    'currency' => env('PAYMENT_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Payment Providers
    |--------------------------------------------------------------------------
    |
    | Configure each supported payment provider below. The 'enabled' flag
    | controls whether the provider is available in the system.
    |
    | To add a new provider:
    | 1. Create a gateway class implementing PaymentGatewayInterface
    | 2. Add the provider to the PaymentProvider enum
    | 3. Register it in PaymentService::registerGateways()
    | 4. Add its config block below
    |
    */

    'providers' => [
        'stripe' => [
            'enabled' => env('STRIPE_ENABLED', true),
        ],
        'paypal' => [
            'enabled' => env('PAYPAL_ENABLED', true),
        ],
        'flutterwave' => [
            'enabled' => env('FLUTTERWAVE_ENABLED', true),
        ],
        'mollie' => [
            'enabled' => env('MOLLIE_ENABLED', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Provider-specific credentials and settings. Each gateway class reads
    | its own env vars in its defaults() method, but you can also configure
    | them here for centralized management.
    |
    */

    'gateways' => [

        'stripe' => [
            'api_key'          => env('STRIPE_SECRET_KEY', ''),
            'publishable_key'  => env('STRIPE_PUBLISHABLE_KEY', ''),
            'webhook_secret'   => env('STRIPE_WEBHOOK_SECRET', ''),
            'currency'         => env('PAYMENT_CURRENCY', 'USD'),
            'test_mode'        => env('STRIPE_TEST_MODE', true),
        ],

        'paypal' => [
            'client_id'    => env('PAYPAL_CLIENT_ID', ''),
            'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
            'webhook_id'   => env('PAYPAL_WEBHOOK_ID', ''),
            'mode'         => env('PAYPAL_MODE', 'sandbox'),
            'currency'     => env('PAYMENT_CURRENCY', 'USD'),
        ],

        'flutterwave' => [
            'public_key'    => env('FLUTTERWAVE_PUBLIC_KEY', ''),
            'secret_key'    => env('FLUTTERWAVE_SECRET_KEY', ''),
            'encryption_key' => env('FLUTTERWAVE_ENCRYPTION_KEY', ''),
            'webhook_secret' => env('FLUTTERWAVE_WEBHOOK_SECRET', ''),
            'currency'      => env('PAYMENT_CURRENCY', 'USD'),
        ],

        'mollie' => [
            'api_key'     => env('MOLLIE_API_KEY', ''),
            'profile_id'  => env('MOLLIE_PROFILE_ID', ''),
            'webhook_url' => env('MOLLIE_WEBHOOK_URL', ''),
            'test_mode'   => env('MOLLIE_TEST_MODE', true),
            'currency'    => env('PAYMENT_CURRENCY', 'EUR'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Paths
    |--------------------------------------------------------------------------
    |
    | The URI paths for each provider's webhook endpoint. These will be
    | registered in your routes/web.php or routes/api.php.
    |
    */

    'webhooks' => [
        'stripe'      => '/webhooks/stripe',
        'paypal'      => '/webhooks/paypal',
        'flutterwave' => '/webhooks/flutterwave',
        'mollie'      => '/webhooks/mollie',
    ],

];

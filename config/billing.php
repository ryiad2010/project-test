<?php

return [
    'payment_gateway_url' => env('PAYMENT_GATEWAY_URL', 'https://api.stripe.com/v1'),
    'api_key' => env('PAYMENT_GATEWAY_API_KEY'),
    'webhook_secret' => env('PAYMENT_WEBHOOK_SECRET'),
    'default_currency' => env('BILLING_DEFAULT_CURRENCY', 'USD'),
    'invoice_prefix' => env('BILLING_INVOICE_PREFIX', 'INV'),
];

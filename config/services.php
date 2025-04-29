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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'nfe' => [
        'token' => env('NFE_API_TOKEN'),
        'url' => env('NFE_API_URL'),
    ],

    'localai' => [
        'url' => env('LOCALAI_URL', 'http://localhost:8080'),
    ],

    'deepseek' => [
        'api_key' => env('DEEPSEEK_API_KEY'),
        'base_uri' => env('DEEPSEEK_BASE_URI', 'https://api.deepseek.com/v1'),
        'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
        'temperature' => env('DEEPSEEK_TEMPERATURE', 0.7),
        'max_tokens' => env('DEEPSEEK_MAX_TOKENS', 2000),
    ],

    'embedding' => [
        'cache_ttl' => env('EMBEDDING_CACHE_TTL', 86400),
    ],

];

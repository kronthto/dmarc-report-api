<?php

use Cekurte\Environment\Environment as Env;

$appBase = __DIR__.'/..';

$dotenv = new Dotenv\Dotenv($appBase);
$dotenv->load();

return [
    'settings' => [
        'displayErrorDetails' => Env::get('APP_ENV') != 'production',
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'tmpReports' => $appBase.'/storage/reports/',

        'allowOrigin' => Env::get('CORS_ORIGIN', '*'),

        'db' => [
            'host' => Env::get('DB_HOST', '127.0.0.1'),
            'user' => Env::get('DB_USERNAME', 'homestead'),
            'pass' => Env::get('DB_PASSWORD', 'secret'),
            'db' => Env::get('DB_DATABASE', 'homestead'),
        ],
    ],
];

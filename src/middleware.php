<?php

// Application middleware

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// CORS
$app->add(function (ServerRequestInterface $request, ResponseInterface $response, callable $next) {
    $allowOrigin = $this->get('settings')['allowOrigin'];

    if (strtoupper($request->getMethod()) == 'OPTIONS') { // Preflight Request
        return $response
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Origin', $allowOrigin)
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Cache-Control, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    }

    /** @var ResponseInterface $response */
    $response = $next($request, $response);

    if ($request->hasHeader('Origin')) {
        $response = $response
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Origin', $allowOrigin)
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Cache-Control, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', $request->getMethod());
    }

    return $response;
});

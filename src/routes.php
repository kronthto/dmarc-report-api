<?php

// Routes

use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

$app->get('/', function (ServerRequestInterface $request, Response $response) {
    return $response->withJson(['status' => 'OK']);
});

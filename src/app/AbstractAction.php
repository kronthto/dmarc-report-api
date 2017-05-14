<?php

namespace App;

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class AbstractAction
{
    /** @var ContainerInterface */
    protected $ci;

    /**
     * Action constructor.
     *
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    abstract public function __invoke(Request $request, Response $response, array $args = []): Response;
}

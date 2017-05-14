<?php

namespace Tests\Functional;

use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /** @var App */
    protected $app;

    protected function setUp()
    {
        parent::setUp();

        // Use the application settings
        $settings = require __DIR__.'/../../src/settings.php';

        // Instantiate the application
        $app = new App($settings);

        // Set up dependencies
        require __DIR__.'/../../src/dependencies.php';

        // Register middleware
        require __DIR__.'/../../src/middleware.php';

        // Register routes
        require __DIR__.'/../../src/routes.php';

        $this->app = $app;
    }

    /**
     * Prepares a test-request.
     *
     * @param string $requestMethod
     * @param string $requestUri
     *
     * @return Request
     */
    public function scaffoldRequest(string $requestMethod, string $requestUri): Request
    {
        // Create a mock environment for testing with
        $environment = Environment::mock(
            [
                'REQUEST_METHOD' => $requestMethod,
                'REQUEST_URI' => $requestUri,
            ]
        );

        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);

        return $request;
    }

    /**
     * Processes the request though the application.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function processRequest(Request $request): Response
    {
        // Set up a response object
        $response = new Response();

        // Process the request/application
        $response = $this->app->process($request, $response);

        // Return the response
        return $response;
    }
}

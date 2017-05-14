<?php

namespace Tests\Functional;

class CorsTest extends BaseTestCase
{
    /**
     * Test a preflight request.
     */
    public function testPreflightRequest()
    {
        $request = $this->scaffoldRequest('OPTIONS', '/');
        $response = $this->processRequest($request);

        $this->assertTrue($response->hasHeader('Access-Control-Allow-Origin'));
    }

    /**
     * Test a CORS request.
     */
    public function testCorsRequest()
    {
        $request = $this->scaffoldRequest('GET', '/')
            ->withHeader('Origin', 'http://foo.bar');
        $response = $this->processRequest($request);

        $this->assertTrue($response->hasHeader('Access-Control-Allow-Origin'));
        $this->assertContains('GET', $response->getHeader('Access-Control-Allow-Methods'));
    }

    /**
     * Assert that the CORS headers only get added if the Origin header is present.
     */
    public function testRequestWithoutOriginDoesNotHaveCorsHeaders()
    {
        $request = $this->scaffoldRequest('GET', '/');
        $response = $this->processRequest($request);

        $this->assertFalse($response->hasHeader('Access-Control-Allow-Origin'));
    }
}

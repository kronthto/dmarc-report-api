<?php

namespace Tests\Functional;

class CrashTest extends BaseTestCase
{
    /**
     * Test that the index route does not produce a server error.
     */
    public function testAppDoesNotCrash()
    {
        $response = $this->runApp('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', json_decode($response->getBody())->status);
    }
}

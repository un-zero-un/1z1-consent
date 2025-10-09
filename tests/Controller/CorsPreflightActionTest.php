<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CorsPreflightActionTest extends WebTestCase
{
    public function testItAddsCorsHeaders(): void
    {
        $client = $this->createClient();
        $client->request('OPTIONS', 'http://localhost/consent', server: [
            'HTTP_HOST' => 'http://localhost',
            'HTTP_REFERER' => 'http://www.localhost',
        ]);

        $this->assertResponseStatusCodeSame(204);

        $responseHeaders = $client->getResponse()->headers;
        $this->assertTrue($responseHeaders->has('Access-Control-Allow-Origin'));
        $this->assertSame('http://www.localhost', $responseHeaders->get('Access-Control-Allow-Origin'));

        $this->assertTrue($responseHeaders->has('Access-Control-Allow-Methods'));
        $this->assertSame('POST', $responseHeaders->get('Access-Control-Allow-Methods'));

        $this->assertTrue($responseHeaders->has('Access-Control-Allow-Headers'));
        $this->assertSame('Content-Type', $responseHeaders->get('Access-Control-Allow-Headers'));

        $this->assertTrue($responseHeaders->has('Access-Control-Allow-Credentials'));
        $this->assertSame('true', $responseHeaders->get('Access-Control-Allow-Credentials'));
    }
}

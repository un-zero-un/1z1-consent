<?php

namespace App\Tests\Application;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LoadApiTest extends WebTestCase
{
    public function testItLoadsConfiguredWebsite(): void
    {
        $client = static::createClient();
        $client->request(
            'GET',
            'https://localhost/api.js',
            server: [
                'HTTP_HOST' => 'localhost',
                'HTTP_REFERER' => 'https://www.localhost/',
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('text/javascript; charset=UTF-8', $client->getResponse()->headers->get('Content-Type'));
        $this->assertStringContainsString(
            'const dialogBox = d.createElement(\'uzu-consent\');',
            $client->getResponse()->getContent()
        );
    }

    public function testUnknownDomainIs404(): void
    {
        $client = static::createClient();
        $client->request(
            'GET',
            'https://localhost/api.js',
            server: [
                'HTTP_HOST' => 'localhost',
                'HTTP_REFERER' => 'https://www.unknown-domain.com/',
            ],
        );

        $this->assertResponseStatusCodeSame(202);
        $this->assertStringContainsString(
            '// No sites configured for this host : www.unknown-domain.com',
            $client->getResponse()->getContent(),
        );
    }
}

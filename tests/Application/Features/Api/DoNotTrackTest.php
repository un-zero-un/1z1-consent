<?php

declare(strict_types=1);

namespace App\Tests\Application\Features\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DoNotTrackTest extends WebTestCase
{
    public function testItRespectsDoNotTrack(): void
    {
        $client = static::createClient();
        $client->request(
            'GET',
            'https://localhost/api.js',
            server: [
                'HTTP_HOST' => 'localhost',
                'HTTP_REFERER' => 'https://www.localhost/',
                'HTTP_DNT' => '1',
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('text/javascript; charset=UTF-8', $client->getResponse()->headers->get('Content-Type'));
        $this->assertStringContainsString(
            'vous souhaitez ne pas être pisté',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'console.log(',
            $client->getResponse()->getContent()
        );
    }
}

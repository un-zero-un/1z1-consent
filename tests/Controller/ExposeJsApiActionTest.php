<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExposeJsApiActionTest extends WebTestCase
{
    public function testNotFoundWebsiteReturns202(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api.js', [], [], [
            'HTTP_REFERER' => 'https://example.com',
            'HTTP_HOST' => 'localhost',
        ]);
        $this->assertResponseStatusCodeSame(202);
        $this->assertStringContainsString('// No sites configured', $client->getResponse()->getContent());
    }

    public function testBypassReturnsBypassResponse(): void
    {
        $client = static::createClient();
        $headers = [
            'HTTP_REFERER' => 'https://www.localhost',
            'HTTP_HOST' => 'localhost',
        ];
        $client->request('GET', '/api.js', [], [], $headers);
        $firstContent = $client->getResponse()->getContent();
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'text/javascript; charset=UTF-8');

        // Second request will be bypassed (cached). It should be identical to first One.
        $client->request('GET', '/api.js', [], [], $headers);
        $secondContent = $client->getResponse()->getContent();
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'text/javascript; charset=UTF-8');

        $this->assertSame($firstContent, $secondContent);
    }

    public function testDntRespectedReturnsRespectMessage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api.js', [], [], [
            'HTTP_REFERER' => 'https://www.localhost',
            'HTTP_HOST' => 'localhost',
            'HTTP_DNT' => '1',
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('ne pas être pisté', $client->getResponse()->getContent());
    }

    public function testNormalCaseRendersTwigAndSavesHit(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api.js', [], [], [
            'HTTP_REFERER' => 'https://www.localhost',
            'HTTP_HOST' => 'localhost',
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'text/javascript; charset=UTF-8');
        $this->assertStringContainsString(
            'const trackerId = \'G-1234567890\';',
            $client->getResponse()->getContent(),
        );
    }

    public function testMissingRefererThrowsException(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api.js', [], [], [
            'HTTP_HOST' => 'localhost',
        ]);
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString('Referer', $client->getResponse()->getContent());
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tracker;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

class PostConsentActionWebTest extends WebTestCase
{
    private array $trackerIds;

    protected function setUp(): void
    {
        self::createClient();
        $trackerRepository = $this->getContainer()->get(EntityManagerInterface::class)->getRepository(Tracker::class);
        $trackers = $trackerRepository->findAll();

        $this->trackerIds = array_map(fn(Tracker$t): string => $t->getId()->toRfc4122(), $trackers);
    }

    public function testNormalConsentCreatesAndSetsCookie(): void
    {
        $client = $this->getClient();
        $trackers = [];
        if (count($this->trackerIds) >= 2) {
            $trackers[$this->trackerIds[0]] = '1';
            $trackers[$this->trackerIds[1]] = '0';
        }

        $client->request('POST', '/consent', [
            'user_id' => 'user-123',
            'tracker' => $trackers,
        ], [], [
            'HTTP_REFERER' => 'https://www.localhost',
            'HTTP_HOST' => 'localhost',
        ]);

        $this->assertResponseStatusCodeSame(204);
        $cookies = $client->getResponse()->headers->getCookies();
        $this->assertNotEmpty($cookies, 'A cookie must be set');
        $this->assertSame('user-123', $cookies[0]->getValue());
    }

    public function testConsentWithoutUserIdReturns400(): void
    {
        $client = $this->getClient();
        $client->request('POST', '/consent', [], [], [
            'HTTP_REFERER' => 'https://www.localhost',
            'HTTP_HOST' => 'localhost',
        ]);
        $this->assertResponseStatusCodeSame(400);
        $this->assertStringContainsString('user_id', $client->getResponse()->getContent());
    }

    public function testConsentWithDntEnabled(): void
    {
        $client = $this->getClient();
        $client->request('POST', '/consent', ['user_id' => 'user-dnt'], [], [
            'HTTP_REFERER' => 'https://www.localhost',
            'HTTP_HOST' => 'localhost',
            'HTTP_DNT' => '1',
        ]);
        $this->assertResponseStatusCodeSame(204);
    }

    public function testConsentCreateThenUpdate(): void
    {
        $client = $this->getClient();
        $trackers = [];
        if (count($this->trackerIds) > 0) {
            $trackers[$this->trackerIds[0]] = '1';
        }

        $client->request('POST', '/consent', [
            'user_id' => 'user-update',
            'tracker' => $trackers,
        ], [], [
            'HTTP_REFERER' => 'https://www.localhost',
            'HTTP_HOST' => 'localhost',
        ]);

        $this->assertResponseStatusCodeSame(204);
        if (count($this->trackerIds) > 0) {
            $trackers[$this->trackerIds[0]] = '0';
        }
        $client->request('POST', '/consent', [
            'user_id' => 'user-update',
            'tracker' => $trackers,
        ], [], [
            'HTTP_REFERER' => 'https://www.localhost',
            'HTTP_HOST' => 'localhost',
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function testConsentWithUnknownSiteReturns404(): void
    {
        $client = $this->getClient();
        $client->request('POST', '/consent', [
            'user_id' => 'user-404',
        ], [], [
            'HTTP_REFERER' => 'https://unknown.com',
            'HTTP_HOST' => 'localhost',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }
}

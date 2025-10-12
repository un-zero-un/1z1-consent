<?php

declare(strict_types=1);

namespace App\Tests\Application\Features\Api;

use App\Entity\Tracker;
use App\Entity\Website;
use App\Tests\Controller\Repositories;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class GlobalPrivacyControlTest extends WebTestCase
{
    use Repositories;

    #[DataProvider('provideGpcValues')]
    public function testItRespectsGlobalPrivacyControl(string $gpcHeader, \Closure $assert): void
    {
        $client = static::createClient();

        $websiteRepository = $this->getRepository(Website::class);
        $website = $websiteRepository->findOneByHostname('www.localhost');

        $googleAnalyticsTracker = $website->trackers
            ->filter(fn (Tracker $tracker) => 'Google Analytics' === $tracker->name)
            ->first();

        $facebookPixelTrack = $website->trackers
            ->filter(fn (Tracker $tracker) => 'Facebook Pixel' === $tracker->name)
            ->first();

        $client->request(
            'GET',
            'https://localhost/api.js',
            server: [
                'HTTP_HOST' => 'localhost',
                'HTTP_REFERER' => 'https://www.localhost/',
                'HTTP_SEC_GPC' => $gpcHeader,
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('text/javascript; charset=UTF-8', $client->getResponse()->headers->get('Content-Type'));

        $assert->bindTo($this)($googleAnalyticsTracker, $facebookPixelTrack, $client);
    }

    public static function provideGpcValues(): array
    {
        return [
            'GPC enabled' => [
                'gpcHeader' => '1',
                'assert' => function (Tracker $googleAnalyticsTracker, Tracker $facebookPixelTrack, $client) {
                    $this->assertStringContainsString($googleAnalyticsTracker->id->toRfc4122(), $client->getResponse()->getContent());
                    $this->assertStringNotContainsString($facebookPixelTrack->id->toRfc4122(), $client->getResponse()->getContent());
                },
            ],
            'GPC disabled' => [
                'gpcHeader' => '0',
                'assert' => function (Tracker $googleAnalyticsTracker, Tracker $facebookPixelTrack, $client) {
                    $this->assertStringContainsString($googleAnalyticsTracker->id->toRfc4122(), $client->getResponse()->getContent());
                    $this->assertStringContainsString($facebookPixelTrack->id->toRfc4122(), $client->getResponse()->getContent());
                },
            ],
        ];
    }
}

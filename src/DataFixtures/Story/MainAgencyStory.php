<?php

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\AdminUserFactory;
use App\DataFixtures\Factory\AgencyFactory;
use App\DataFixtures\Factory\ClientFactory;
use App\DataFixtures\Factory\TrackerFactory;
use App\DataFixtures\Factory\WebsiteDomainFactory;
use App\DataFixtures\Factory\WebsiteFactory;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'main_agency', groups: ['main'])]
final class MainAgencyStory extends Story
{
    public function __construct(#[Autowire('%env(MAIN_DOMAIN)%')] private readonly string $mainDomain)
    {
    }

    public function build(): void
    {
        $unZeroUn = AgencyFactory::createOne([
                'name' => 'Un Zéro Un',
                'host' => $this->mainDomain,
            ]);

        $testClient = ClientFactory::createOne([
            'agency' => $unZeroUn,
            'name' => 'Client de test',
        ]);

        WebsiteFactory::createOne([
            'client' => $testClient,
            'domains' => [
                WebsiteDomainFactory::createOne(['domain' => 'www.'.$this->mainDomain]),
            ],
            'trackers' => [
                TrackerFactory::new()->googleAnalytics()->create(),
                TrackerFactory::new()->custom()->create(),
            ],
        ]);

        AdminUserFactory::new()
            ->admin()
            ->with([
                'agency' => $unZeroUn,
                'email' => 'admin@example.com',
            ])
            ->create();
    }
}

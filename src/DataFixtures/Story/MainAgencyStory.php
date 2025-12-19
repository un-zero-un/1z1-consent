<?php

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\AdminUserFactory;
use App\DataFixtures\Factory\AgencyFactory;
use App\DataFixtures\Factory\ClientFactory;
use App\DataFixtures\Factory\PersonFactory;
use App\DataFixtures\Factory\ServerFactory;
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

        $dataResponsible = PersonFactory::createOne([
            'client' => $testClient,
            'country' => 'FR',
        ]);

        $dpo = PersonFactory::createOne([
            'client' => $testClient,
            'country' => 'FR',
        ]);

        $testClient->dataResponsible = $dataResponsible;
        $testClient->dpo = $dpo;

        $server1 = ServerFactory::createOne(['agency' => $unZeroUn, 'name' => 'Serveur 1']);

        $website = WebsiteFactory::createOne(['client' => $testClient, 'server' => $server1]);
        WebsiteDomainFactory::createOne(['domain' => 'www.'.$this->mainDomain, 'website' => $website]);
        TrackerFactory::new()->with(['website' => $website])->googleAnalytics('G-1234567890')->create();
        TrackerFactory::new()->with(['website' => $website])->facebook()->create();
        TrackerFactory::new()->with(['website' => $website])->custom()->create();

        AdminUserFactory::new()
            ->admin()
            ->with([
                'agency' => $unZeroUn,
                'email' => 'admin@example.com',
            ])
            ->create();

        AdminUserFactory::new()
            ->with([
                'email' => 'agency@example.com',
            ])
            ->create();
    }
}

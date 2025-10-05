<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use App\Entity\Client;
use App\Entity\Tracker;
use App\Entity\Website;
use App\Entity\WebsiteDomain;
use App\ValueObject\TrackerType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class AppFixtures extends Fixture
{
    public function __construct(
        #[Autowire('%main_domain%')]
        private readonly string $mainDomain,
    )
    {

    }

    public function load(ObjectManager $manager): void
    {
        $agency = new Agency();
        $agency->setName('Un Zéro Un');
        $agency->setHost($this->mainDomain);
        $manager->persist($agency);

        $testClient = new Client();
        $testClient->setAgency($agency);
        $testClient->setName('Client de test');
        $manager->persist($testClient);

        $localhostDomain = new WebsiteDomain();
        $localhostDomain->setDomain('www.' . $this->mainDomain);
        $manager->persist($localhostDomain);

        $localhost = new Website();
        $localhost->setClient($testClient);
        $localhost->addDomain($localhostDomain);
        $manager->persist($localhost);

        $localhostGoogleAnalytics = new Tracker();
        $localhostGoogleAnalytics->setWebsite($localhost);
        $localhostGoogleAnalytics->setType(TrackerType::GOOGLE_ANALYTICS);
        $localhostGoogleAnalytics->setName('Google Analytics');
        $localhostGoogleAnalytics->setTrackerId('G-EFGBFLCDZX');
        $manager->persist($localhostGoogleAnalytics);

        $localhostCustomTracker = new Tracker();
        $localhostCustomTracker->setWebsite($localhost);
        $localhostCustomTracker->setType(TrackerType::OTHER);
        $localhostCustomTracker->setName('Tracker sur mesure');
        $localhostCustomTracker->setTrackerId('custom-code');
        $localhostCustomTracker->setCustomCode(/* @lang javascript */ 'console.log(`\n * * Hello ${trackerId}! * * \n`)');
        $manager->persist($localhostCustomTracker);

        $manager->flush();
    }
}

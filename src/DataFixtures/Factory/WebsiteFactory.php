<?php

namespace App\DataFixtures\Factory;

use App\Entity\Website;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Website>
 */
final class WebsiteFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Website::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'client' => ClientFactory::first(),
            'addAccessLogToGDPR' => true,
            'addTrackerToGDPR' => true,
            'respectDoNotTrack' => true,
            'showOpenButton' => true,
        ];
    }
}

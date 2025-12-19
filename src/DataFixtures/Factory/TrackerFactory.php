<?php

namespace App\DataFixtures\Factory;

use App\Entity\Tracker;
use App\ValueObject\TrackerType;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Tracker>
 */
final class TrackerFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Tracker::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->sentence(),
            'trackerId' => self::faker()->text(),
            'type' => self::faker()->randomElement(TrackerType::cases()),
            'useDefaultSnippet' => self::faker()->boolean(),
            'website' => WebsiteFactory::first(),
            'gpcCompliant' => true,
        ];
    }

    public function googleAnalytics(?string $trackerId = null): self
    {
        return $this->with([
            'name' => 'Google Analytics',
            'type' => TrackerType::GOOGLE_ANALYTICS,
            'trackerId' => $trackerId ?? self::faker()->bothify('G-##########'),
        ]);
    }

    public function facebook(): self
    {
        return $this->with([
            'name' => 'Facebook Pixel',
            'type' => TrackerType::FACEBOOK_PIXEL,
            'trackerId' => self::faker()->asciify('**-******-**'),
            'gpcCompliant' => false,
        ]);
    }

    public function custom(): self
    {
        return $this->with([
            'name' => 'Tracker sur mesure',
            'type' => TrackerType::OTHER,
            'trackerId' => 'custom-code',
            'customCode' => 'console.log(`\n * * Hello ${trackerId}! * * \n`)',
        ]);
    }
}

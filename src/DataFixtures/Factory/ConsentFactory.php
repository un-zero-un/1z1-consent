<?php

namespace App\DataFixtures\Factory;

use App\Entity\Consent;
use Symfony\Component\Uid\Uuid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Consent>
 */
final class ConsentFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Consent::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'gpcEnabled' => self::faker()->boolean(),
            'userId' => Uuid::v4(),
            'website' => WebsiteFactory::random(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (Consent $consent): void {
                foreach ($consent->website->trackers as $tracker) {
                    if (!$tracker->gpcCompliant && $consent->gpcEnabled) {
                        continue;
                    }

                    $consent
                        ->setTrackerConsent($tracker->id, self::faker()->boolean());
                }

                $reflectionClass = new \ReflectionClass(Consent::class);
                $reflectionClass->getProperty('createdAt')->setValue(
                    $consent,
                    \DateTimeImmutable::createFromInterface(self::faker()->dateTimeBetween('-2 years')),
                );
                $reflectionClass->getProperty('updatedAt')->setValue(
                    $consent,
                    \DateTimeImmutable::createFromInterface(
                        self::faker()->dateTimeBetween($consent->getCreatedAt()->format('c')),
                    ),
                );
            })
        ;
    }
}

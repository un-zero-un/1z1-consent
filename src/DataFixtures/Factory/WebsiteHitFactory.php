<?php

namespace App\DataFixtures\Factory;

use App\Entity\WebsiteHit;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<WebsiteHit>
 */
final class WebsiteHitFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return WebsiteHit::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        $website = WebsiteFactory::random();

        return [
            'ipAddress' => self::faker()->boolean(75) ? self::faker()->ipv4() : self::faker()->ipv6(),
            'referer' => 'https://'.self::faker()->randomElement($website->domains)->domain.'/'.self::faker()->word(),
            'website' => WebsiteFactory::random(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return parent::initialize()
            ->afterInstantiate(function (WebsiteHit $websiteHit): void {
                $reflectionClass = new \ReflectionClass(WebsiteHit::class);
                $property = $reflectionClass->getProperty('createdAt');
                $property->setValue(
                    $websiteHit,
                    \DateTimeImmutable::createFromMutable(
                        self::faker()->dateTimeBetween('-2 years'),
                    ),
                );
            });
    }
}

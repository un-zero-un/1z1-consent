<?php

namespace App\DataFixtures\Factory;

use App\Entity\WebsiteDomain;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<WebsiteDomain>
 */
final class WebsiteDomainFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return WebsiteDomain::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'domain' => self::faker()->domainName(),
        ];
    }
}

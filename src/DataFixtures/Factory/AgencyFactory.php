<?php

namespace App\DataFixtures\Factory;

use App\Entity\Agency;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Agency>
 */
final class AgencyFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Agency::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'host' => self::faker()->domainName(),
            'name' => self::faker()->sentence(),
        ];
    }
}

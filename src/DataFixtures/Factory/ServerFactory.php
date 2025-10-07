<?php

namespace App\DataFixtures\Factory;

use App\Entity\Server;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Server>
 */
final class ServerFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Server::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'agency' => AgencyFactory::random(),
            'name' => self::faker()->sentence(),
            'numberOfUnmanagedSites' => self::faker()->numberBetween(1, 5),
            'quantityOfCO2eqPerYear' => self::faker()->numberBetween(100, 300),
        ];
    }
}

<?php

namespace App\DataFixtures\Factory;

use App\Entity\Client;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Client>
 */
final class ClientFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Client::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'agency' => AgencyFactory::first(),
            'name' => self::faker()->sentence(),
        ];
    }
}

<?php

namespace App\DataFixtures\Factory;

use App\Entity\Person;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Person>
 */
final class PersonFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Person::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        $firstName = self::faker()->firstName();
        $lastName = self::faker()->lastName();
        $email = sprintf(
            '%s.%s@example.com',
            new AsciiSlugger()->slug($firstName)->lower(),
            new AsciiSlugger()->slug($lastName)->lower(),
        );

        return [
            'client' => ClientFactory::new(),
            'country' => self::faker()->country(),
            'email' => $email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'phoneNumber' => self::faker()->phoneNumber(),
            'address' => self::faker()->streetAddress(),
            'postCode' => self::faker()->postcode(),
            'city' => self::faker()->city(),
        ];
    }
}

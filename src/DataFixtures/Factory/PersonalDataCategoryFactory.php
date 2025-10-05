<?php

namespace App\DataFixtures\Factory;

use App\Entity\PersonalDataCategory;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<PersonalDataCategory>
 */
final class PersonalDataCategoryFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return PersonalDataCategory::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->sentence(),
        ];
    }
}

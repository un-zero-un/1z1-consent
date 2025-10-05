<?php

namespace App\DataFixtures\Factory;

use App\Entity\SensitiveDataCategory;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<SensitiveDataCategory>
 */
final class SensitiveDataCategoryFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return SensitiveDataCategory::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->text(),
        ];
    }
}

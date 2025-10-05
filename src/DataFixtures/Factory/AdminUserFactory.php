<?php

namespace App\DataFixtures\Factory;

use App\Entity\AdminUser;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<AdminUser>
 */
final class AdminUserFactory extends PersistentObjectFactory
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    #[\Override]
    public static function class(): string
    {
        return AdminUser::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'agency' => AgencyFactory::new(),
            'email' => self::faker()->email(),
            'roles' => [],
        ];
    }

    public function admin(): self
    {
        return $this->with(['roles' => ['ROLE_ADMIN']]);
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (AdminUser $adminUser): void {
                $hashedPassword = $this->userPasswordHasher->hashPassword($adminUser, '!ChangeMe!');
                $adminUser->setPassword($hashedPassword);
            })
        ;
    }
}

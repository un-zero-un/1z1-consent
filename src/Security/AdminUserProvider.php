<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\AdminUser;
use App\Repository\AdminUserRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @psalm-suppress UnusedClass
 *
 * @implements UserProviderInterface<AdminUser>
 */
readonly class AdminUserProvider implements UserProviderInterface
{
    public function __construct(private AdminUserRepository $adminUserRepository)
    {
    }

    public function refreshUser(UserInterface $user): AdminUser
    {
        $adminUser = $this->adminUserRepository->findOneByEmail($user->getUserIdentifier());
        if (!$adminUser) {
            throw new UserNotFoundException();
        }

        return $adminUser;
    }

    public function supportsClass(string $class): bool
    {
        return AdminUser::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->adminUserRepository->findOneByEmail($identifier);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        return $user;
    }
}

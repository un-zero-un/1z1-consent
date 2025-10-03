<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AdminUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AdminUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminUser::class);
    }

    public function findOneByEmail(string $email): ?AdminUser
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findOneByGoogleId(string $googleId): ?AdminUser
    {
        return $this->findOneBy(['googleId' => $googleId]);
    }

    public function save(AdminUser $adminUser): void
    {
        $this->getEntityManager()->persist($adminUser);
        $this->getEntityManager()->flush();
    }
}

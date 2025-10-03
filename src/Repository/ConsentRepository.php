<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Consent;
use App\Entity\Website;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

final class ConsentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consent::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findOneByWebsiteAndUserId(Website $website, string $userId): Consent
    {
        return $this
            ->createQueryBuilder('consent')
            ->where('consent.website = :website')
            ->andWhere('consent.userId = :userId')
            ->setParameter('website', $website)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleResult();
    }

    public function getCountByWebsiteGroupedByMonthOnAYear(Website $website): array
    {
        return $this->createQueryBuilder('consent')
                    ->select('COUNT(consent.id) AS count, MONTH(consent.createdAt) AS month, YEAR(consent.createdAt) AS year')
                    ->groupBy('month, year')
                    ->orderBy('year, month', 'ASC')
                    ->where('consent.website = :website')
                    ->andWhere('consent.createdAt > :date')
                    ->setParameter('website', $website)
                    ->setParameter('date', new \DateTimeImmutable('-1 year 1 month'))
                    ->getQuery()
                    ->getArrayResult();
    }

    public function getCountByWebsiteGroupedByDayOnAMonth(Website $website): array
    {
        return $this->createQueryBuilder('consent')
                    ->select('COUNT(consent.id) AS count, DAY(consent.createdAt) AS day, MONTH(consent.createdAt) AS month, YEAR(consent.createdAt) AS year')
                    ->groupBy('day, month, year')
                    ->orderBy('year, month, day', 'ASC')
                    ->where('consent.website = :website')
                    ->andWhere('consent.createdAt > :date')
                    ->setParameter('website', $website)
                    ->setParameter('date', new \DateTimeImmutable('-31 days'))
                    ->getQuery()
                    ->getArrayResult();
    }

    public function save(Consent $consent): void
    {
        $this->getEntityManager()->persist($consent);
        $this->getEntityManager()->flush();
    }

    public function update(Consent $consent): void
    {
        $this->getEntityManager()->flush();
    }
}

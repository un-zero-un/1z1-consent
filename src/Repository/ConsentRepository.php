<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Consent;
use App\Entity\Website;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;

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
            ->innerJoin('consent.website', 'website')
            ->where('website.id = :website_id')
            ->andWhere('consent.userId = :user_id')
            ->setParameter('website_id', $website->id, UuidType::NAME)
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getSingleResult();
    }

    public function getCountByWebsiteGroupedByMonthOnAYear(Website $website): array
    {
        return $this->createQueryBuilder('consent')
                    ->innerJoin('consent.website', 'website')
                    ->select('COUNT(consent.id) AS count, EXTRACT_DATE_PART(consent.createdAt, \'month\') AS month, EXTRACT_DATE_PART(consent.createdAt, \'year\') AS year')
                    ->groupBy('month, year')
                    ->orderBy('year, month', 'ASC')
                    ->where('website.id = :website_id')
                    ->andWhere('consent.createdAt > :date')
                    ->setParameter('website_id', $website->id, UuidType::NAME)
                    ->setParameter('date', new \DateTimeImmutable('-1 year 1 month'))
                    ->getQuery()
                    ->getArrayResult();
    }

    public function getCountByWebsiteGroupedByDayOnAMonth(Website $website): array
    {
        return $this->createQueryBuilder('consent')
                    ->innerJoin('consent.website', 'website')
                    ->select('COUNT(consent.id) AS count, EXTRACT_DATE_PART(consent.createdAt, \'day\') AS day, EXTRACT_DATE_PART(consent.createdAt, \'month\') AS month, EXTRACT_DATE_PART(consent.createdAt, \'year\') AS year')
                    ->groupBy('day, month, year')
                    ->orderBy('year, month, day', 'ASC')
                    ->where('website.id = :website_id')
                    ->andWhere('consent.createdAt > :date')
                    ->setParameter('website_id', $website->id, UuidType::NAME)
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

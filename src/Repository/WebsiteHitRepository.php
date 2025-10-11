<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Website;
use App\Entity\WebsiteHit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * @api
 */
class WebsiteHitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WebsiteHit::class);
    }

    public function getCountByWebsiteGroupedByMonthOnAYear(Website $website): array
    {
        return $this->createQueryBuilder('h')
                    ->select('COUNT(h.id) AS count, SUBSTRING(h.createdAt, 5, 2) AS month, SUBSTRING(h.createdAt, 0, 4) AS year')
                    ->innerJoin('h.website', 'website')
                    ->groupBy('month, year')
                    ->orderBy('year, month', 'ASC')
                    ->where('website.id = :website_id')
                    ->andWhere('h.createdAt > :date')
                    ->setParameter('website_id', $website->id, UuidType::NAME)
                    ->setParameter('date', new \DateTimeImmutable('-1 year 1 month'))
                    ->getQuery()
                    ->getArrayResult();
    }

    public function getCountByWebsiteGroupedByDayOnAMonth(Website $website): array
    {
        return $this->createQueryBuilder('h')
                        ->select('COUNT(h.id) AS count, SUBSTRING(h.createdAt, 8, 2) AS day, SUBSTRING(h.createdAt, 5, 2) AS month, SUBSTRING(h.createdAt, 0, 4) AS year')
                        ->innerJoin('h.website', 'website')
                        ->groupBy('day, month, year')
                        ->orderBy('year, month, day', 'ASC')
                        ->where('website.id = :website_id')
                        ->andWhere('h.createdAt > :date')
                        ->setParameter('website_id', $website->id, UuidType::NAME)
                        ->setParameter('date', new \DateTimeImmutable('-31 days'))
                        ->getQuery()
                        ->getArrayResult();
    }

    public function save(WebsiteHit $websiteHit): void
    {
        $this->getEntityManager()->persist($websiteHit);
        $this->getEntityManager()->flush();
    }

    public function saveFromRawData(string $websiteId, ?string $ipAddress, string $referer): void
    {
        $this->getEntityManager()->getConnection()->executeStatement(
            'INSERT INTO website_hit (id, website_id, ip_address, referer, created_at) VALUES (:id, :websiteId, :ipAddress, :referer, :now)',
            [
                'id' => Uuid::v7()->toRfc4122(),
                'websiteId' => $websiteId,
                'ipAddress' => $ipAddress,
                'referer' => $referer,
                'now' => new \DateTimeImmutable(),
            ],
            [
                'id' => UuidType::NAME,
                'websiteId' => UuidType::NAME,
                'ipAddress' => Types::STRING,
                'referer' => Types::STRING,
                'now' => Types::DATETIME_IMMUTABLE,
            ]
        );
    }
}

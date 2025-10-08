<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Website;
use App\Entity\WebsiteHit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
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
                    ->select('COUNT(h.id) as count, MONTH(h.createdAt) as month, YEAR(h.createdAt) as year')
                    ->groupBy('month, year')
                    ->orderBy('year, month', 'ASC')
                    ->where('h.website = :website')
                    ->andWhere('h.createdAt > :date')
                    ->setParameter('website', $website)
                    ->setParameter('date', new \DateTimeImmutable('-1 year 1 month'))
                    ->getQuery()
                    ->getArrayResult();
    }

    public function getCountByWebsiteGroupedByDayOnAMonth(Website $website): array
    {
        return $this->createQueryBuilder('h')
                    ->select('COUNT(h.id) as count, DAY(h.createdAt) as day, MONTH(h.createdAt) as month, YEAR(h.createdAt) as year')
                    ->groupBy('day, month, year')
                    ->orderBy('year, month, day', 'ASC')
                    ->where('h.website = :website')
                    ->andWhere('h.createdAt > :date')
                    ->setParameter('website', $website)
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
            'INSERT INTO website_hit (id, website_id, ip_address, referer, created_at) VALUES (:id, :websiteId, :ipAddress, :referer, NOW())',
            [
                'id' => Uuid::v6()->toRfc4122(),
                'websiteId' => $websiteId,
                'ipAddress' => $ipAddress,
                'referer' => $referer,
            ],
        );
    }
}

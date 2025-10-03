<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Website;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

final class WebsiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Website::class);
    }

    /**
     * @throws NonUniqueResultException|NoResultException
     */
    public function findOneById(string $id): Website
    {
        return $this
            ->createQueryBuilder('website')
            ->where('website.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->enableResultCache(60)
            ->getSingleResult();
    }

    /**
     * @throws NonUniqueResultException|NoResultException
     */
    public function findOneByHostname(string $hostname): Website
    {
        return $this
            ->createQueryBuilder('website')
            ->innerJoin('website.domains', 'domains')
            ->where('domains.domain = :hostname')
            ->setParameter('hostname', $hostname)
            ->setMaxResults(1)
            ->getQuery()
            ->enableResultCache(60)
            ->getSingleResult();
    }
}

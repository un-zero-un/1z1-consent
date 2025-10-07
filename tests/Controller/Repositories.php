<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\Container;

trait Repositories
{
    public function getRepository(string $entityClass): EntityRepository
    {
        return self::getContainer()->get('doctrine')->getRepository($entityClass);
    }

    abstract protected static function getContainer(): Container;
}

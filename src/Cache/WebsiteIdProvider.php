<?php

declare(strict_types=1);

namespace App\Cache;

use App\Exception\HostMismatchException;
use App\Exception\WebsiteNotFoundException;
use App\Repository\WebsiteRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Contracts\Cache\CacheInterface;

final readonly class WebsiteIdProvider
{
    public function __construct(
        private CacheInterface $websiteIdProviderCache,
        private WebsiteRepository $websiteRepository,
    ) {
    }

    public function get(string $refererHostname, string $hostname): string
    {
        return $this->websiteIdProviderCache->get(
            $refererHostname,
            function () use ($refererHostname, $hostname) {
                try {
                    $website = $this->websiteRepository->findOneByHostname($refererHostname);
                } catch (NoResultException) {
                    throw new WebsiteNotFoundException(['referer' => $refererHostname, 'host' => $hostname]);
                }

                $expected = $website->client?->getAgency()?->host;
                assert(null !== $expected);
                if ($expected !== $hostname) {
                    throw new HostMismatchException($expected, $hostname);
                }

                return $website->id->toRfc4122();
            },
        );
    }
}

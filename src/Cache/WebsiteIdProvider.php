<?php

declare(strict_types=1);

namespace App\Cache;

use App\Repository\WebsiteRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
                    throw new NotFoundHttpException('Website not found');
                }

                if ($website->getClient()?->getAgency()?->getHost() !== $hostname) {
                    throw new BadRequestException('Host mismatch');
                }

                return $website->getId()->toRfc4122();
            },
        );
    }
}

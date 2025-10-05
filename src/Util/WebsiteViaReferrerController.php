<?php

declare(strict_types=1);

namespace App\Util;

use App\Entity\Website;
use App\Repository\WebsiteRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait WebsiteViaReferrerController
{
    public function getReferer(Request $request): ?string
    {
        return $request->headers->get('referer');
    }

    private function getRefererHost(Request $request): string
    {
        $referer = $this->getReferer($request);
        if (null === $referer) {
            throw new BadRequestException('Missing referer header');
        }

        $host = parse_url($referer, PHP_URL_HOST);
        if (!is_string($host)) {
            throw new BadRequestException('Unable to parse referer host');
        }

        return $host;
    }

    /**
     * @throws NonUniqueResultException
     */
    private function getWebsiteWithHostname(WebsiteRepository $websiteRepository, Request $request, string $hostname): Website
    {
        try {
            $website = $websiteRepository->findOneByHostname($hostname);
            if ($website->getClient()?->getAgency()?->getHost() !== $request->getHost()) {
                throw new BadRequestException('Host mismatch');
            }

            return $website;
        } catch (NoResultException) {
            throw new NotFoundHttpException('Unknown website');
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    private function getWebsiteWithRequest(WebsiteRepository $websiteRepository, Request $request): Website
    {
        $refererHost = $this->getRefererHost($request);

        return $this->getWebsiteWithHostname($websiteRepository, $request, $refererHost);
    }
}

<?php

declare(strict_types=1);

namespace App\Util;

use App\Entity\Website;
use App\Exception\HostMismatchException;
use App\Exception\MalformedHostHeaderException;
use App\Exception\MissingRefererException;
use App\Exception\WebsiteNotFoundException;
use App\Repository\WebsiteRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Request;

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
            throw new MissingRefererException();
        }

        $host = parse_url($referer, PHP_URL_HOST);
        if (!is_string($host)) {
            throw new MalformedHostHeaderException();
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
            $expectedHost = $website->client?->getAgency()?->host;

            assert(null !== $expectedHost);
            if ($expectedHost !== $request->getHost()) {
                throw new HostMismatchException($expectedHost, $request->getHost());
            }

            return $website;
        } catch (NoResultException) {
            throw new WebsiteNotFoundException(['website_hostname' => $hostname]);
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

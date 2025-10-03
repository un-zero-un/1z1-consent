<?php

declare(strict_types=1);

namespace App\Cache;

use App\Repository\WebsiteHitRepository;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;

final readonly class ApiBypasser
{
    private const CACHE_PREFIX = 'api_bypass__';
    private const EXPIRATION = 3_600 * 24;

    public function __construct(private CacheInterface $cache, private WebsiteHitRepository $websiteHitRepository)
    {
    }

    public function canBypass(Fingerprint $fingerprint): bool
    {
        if (null !== $this->cache->get(self::CACHE_PREFIX.$fingerprint->getHash(), fn () => null)) {
            return true;
        }

        return false;
    }

    public function bypass(Fingerprint $fingerprint, Request $request): Response
    {
        $this->websiteHitRepository->saveFromRawData(
            $fingerprint->getWebsiteId(),
            $request->getClientIp() ?: '',
            $request->headers->get('referer') ?: '',
        );

        /* @psalm-suppress NoValue Supppressed because of the particular pattern used here */
        return $this->cache->get(
            self::CACHE_PREFIX.$fingerprint->getHash(),
            fn () => throw new \RuntimeException('This should not happen'),
        );
    }

    public function save(Fingerprint $fingerprint, Response $response): void
    {
        $this->cache->delete(self::CACHE_PREFIX.$fingerprint->getHash());
        $this->cache->get(
            self::CACHE_PREFIX.$fingerprint->getHash(),
            function (CacheItemInterface $item) use ($response) {
                $item->expiresAfter(self::EXPIRATION);
                $item->set($response);

                return $response;
            },
        );
    }

    public function remove(Fingerprint $fingerprint): void
    {
        $this->cache->delete(self::CACHE_PREFIX.$fingerprint->getHash());
    }
}

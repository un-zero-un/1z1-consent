<?php

declare(strict_types=1);

namespace App\Cache;

use App\Repository\WebsiteHitRepository;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @psalm-suppress NoValue Suppressed because of the particular pattern used here
 */
final readonly class ApiBypasser
{
    private const int EXPIRATION = 3_600 * 24;

    public function __construct(
        private CacheInterface $apiBypasserCache,
        private WebsiteHitRepository $websiteHitRepository,
    ) {
    }

    public function canBypass(Fingerprint $fingerprint): bool
    {
        if (null !== $this->apiBypasserCache->get($fingerprint->getHash(), fn () => null)) {
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

        return $this->apiBypasserCache->get(
            $fingerprint->getHash(),
            fn () => throw new \RuntimeException('This should not happen'),
        );
    }

    public function save(Fingerprint $fingerprint, Response $response): void
    {
        $this->apiBypasserCache->delete($fingerprint->getHash());
        $this->apiBypasserCache->get(
            $fingerprint->getHash(),
            function (CacheItemInterface $item) use ($response) {
                $item->expiresAfter(self::EXPIRATION);
                $item->set($response);

                return $response;
            },
        );
    }

    public function remove(Fingerprint $fingerprint): void
    {
        $this->apiBypasserCache->delete($fingerprint->getHash());
    }
}

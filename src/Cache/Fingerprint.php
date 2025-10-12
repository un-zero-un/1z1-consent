<?php

declare(strict_types=1);

namespace App\Cache;

use Symfony\Component\HttpFoundation\Request;

/**
 * @api
 */
readonly class Fingerprint
{
    public private(set) bool $dnt;

    public private(set) bool $gpc;

    private function __construct(
        private(set) Request $request,
        private string $refererHostname,
        private string $websiteId,
    ) {
        $this->dnt = '1' === $request->headers->get('dnt');
        $this->gpc = '1' === $request->headers->get('sec-gpc');
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    public function getHash(): string
    {
        return hash(
            'sha256',
            $this->refererHostname.'#'.
            $this->request->getHost().'#'.
            $this->websiteId.'#'.
            ($this->dnt ? 'DNT' : 'TRACK').'#'.
            ($this->gpc ? 'GPC' : 'NO_GPC'),
        );
    }

    public static function create(Request $request, string $refererHostname, string $websiteId): self
    {
        return new self($request, $refererHostname, $websiteId);
    }
}

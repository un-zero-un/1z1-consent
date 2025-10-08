<?php

declare(strict_types=1);

namespace App\Cache;

use Symfony\Component\HttpFoundation\Request;

/**
 * @api
 */
readonly class Fingerprint
{
    private function __construct(
        private Request $request,
        private string $refererHostname,
        private string $websiteId,
        private bool $dntEnabled,
    ) {
    }

    public function getHash(): string
    {
        return hash(
            'sha256',
            $this->refererHostname.'#'.
            ($this->request->headers->get('dnt') ?: uniqid()).'#'.
            $this->request->getHost().'#'.
            $this->websiteId.'#'.
            ($this->dntEnabled ? 'DNT' : 'TRACK'),
        );
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getRefererHostname(): string
    {
        return $this->refererHostname;
    }

    public function getWebsiteId(): string
    {
        return $this->websiteId;
    }

    public static function create(Request $request, string $refererHostname, string $websiteId, bool $dnt): self
    {
        return new self($request, $refererHostname, $websiteId, $dnt);
    }
}

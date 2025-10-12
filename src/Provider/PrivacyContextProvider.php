<?php

namespace App\Provider;

use App\Entity\Website;
use App\ValueObject\PrivacyContext;
use Symfony\Component\HttpFoundation\Request;

final readonly class PrivacyContextProvider
{
    public function __construct(private bool $dntEnabled)
    {
    }

    public function getContext(Website $website, Request $request): PrivacyContext
    {
        return new PrivacyContext(
            doNotTrack: $this->dntEnabled && $website->respectDoNotTrack && '1' === $request->headers->get('dnt'),
            globalPrivacyControl: $website->respectGlobalPrivacyControl && '1' === $request->headers->get('sec-gpc'),
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Util;

use App\Exception\MissingRefererException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait CorsController
{
    private function appendCorsHeaders(Request $request, Response $response): void
    {
        $referer = $request->headers->get('referer');
        if (null === $referer) {
            throw new MissingRefererException();
        }

        $parsedReferer = parse_url($referer);

        $response->headers->set('Access-Control-Allow-Methods', 'POST');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        $response->headers->set(
            'Access-Control-Allow-Origin',
            ($parsedReferer['scheme'] ?? 'https').'://'.($parsedReferer['host'] ?? $request->getHost()),
        );
    }
}

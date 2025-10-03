<?php

declare(strict_types=1);

namespace App\Controller;

use App\Cache\ApiBypasser;
use App\Cache\Fingerprint;
use App\Cache\WebsiteIdProvider;
use App\Repository\WebsiteHitRepository;
use App\Repository\WebsiteRepository;
use App\Util\WebsiteViaReferrerController;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route(path: '/api.js', name: 'expose_js_api', methods: ['GET'], format: 'js')]
#[Template('exposeJsApi.js.twig')]
#[AsController]
final readonly class ExposeJsApiAction
{
    use WebsiteViaReferrerController;

    public function __construct(
        private WebsiteRepository $websiteRepository,
        private Environment $twig,
        private WebsiteHitRepository $websiteHitRepository,
        private WebsiteIdProvider $websiteIdProvider,
        private ApiBypasser $apiBypasser,
        private bool $dntEnabled,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $dnt = '1' === $request->headers->get('dnt');
        $refererHost = $this->getRefererHost($request);

        try {
            $websiteId = $this->websiteIdProvider->get($refererHost, $request->getHost());
        } catch (NotFoundHttpException) {
            // Returning a non errored response, even if the website is not found.
            return new Response('// No sites configured for this host : '.$refererHost, 202, ['Content-Type' => 'text/javascript']);
        }

        $fingerPrint = Fingerprint::create($request, $refererHost, $websiteId, $dnt);

        if ($this->apiBypasser->canBypass($fingerPrint)) {
            return $this->apiBypasser->bypass($fingerPrint, $request);
        }

        $website = $this->websiteRepository->findOneById($websiteId);
        if (
            $dnt
            && $this->dntEnabled
            && $website->isRespectDoNotTrack()
        ) {
            return new Response(
                'console.log("Votre navigateur nous indique que vous souhaitez ne pas être pisté. Nous comprenons et respectons ce choix, donc nous ne chargerons même pas notre script 🙂");',
                200,
                ['Content-Type' => 'text/javascript'],
            );
        }

        $response = new Response(
            $this->twig->render('exposeJsApi.js.twig', ['website' => $website]),
            200,
            ['Content-Type' => 'text/javascript'],
        );

        $referer = $this->getReferer($request);
        if (null === $referer) {
            throw new \RuntimeException('Referer is required');
        }

        $ip = $request->getClientIp();
        if (null === $ip) {
            throw new \RuntimeException('Client IP is required');
        }

        $this->websiteHitRepository->saveFromRawData($website->getId()->toRfc4122(), $ip, $referer);
        $this->apiBypasser->save($fingerPrint, $response);

        return $response;
    }
}

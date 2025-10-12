<?php

declare(strict_types=1);

namespace App\Controller;

use App\Cache\ApiBypasser;
use App\Cache\Fingerprint;
use App\Cache\WebsiteIdProvider;
use App\Entity\Tracker;
use App\Exception\MissingRefererException;
use App\Provider\PrivacyContextProvider;
use App\Repository\WebsiteHitRepository;
use App\Repository\WebsiteRepository;
use App\Util\WebsiteViaReferrerController;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
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
        private PrivacyContextProvider $privacyContextProvider,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $refererHost = $this->getRefererHost($request);

        try {
            $websiteId = $this->websiteIdProvider->get($refererHost, $request->getHost());
        } catch (NotFoundHttpException) {
            // Returning a non errored response, even if the website is not found.
            return new Response('// No sites configured for this host : '.$refererHost, 202, ['Content-Type' => 'text/javascript']);
        }

        $fingerPrint = Fingerprint::create($request, $refererHost, $websiteId);

        if ($this->apiBypasser->canBypass($fingerPrint)) {
            return $this->apiBypasser->bypass($fingerPrint, $request);
        }

        $website = $this->websiteRepository->findOneById($websiteId);
        $privacyContext = $this->privacyContextProvider->getContext($website, $request);
        if ($privacyContext->doNotTrack) {
            return new Response(
                'console.log("Votre navigateur nous indique que vous souhaitez ne pas être pisté. Nous comprenons et respectons ce choix, donc nous ne chargerons même pas notre script 🙂");',
                200,
                ['Content-Type' => 'text/javascript'],
            );
        }

        $response = new Response(
            $this->twig->render(
                'exposeJsApi.js.twig',
                [
                    'website' => $website,
                    'trackers' => $website->trackers
                        ->filter(static fn (Tracker $tracker) => $tracker->gpcCompliant || !$privacyContext->globalPrivacyControl)
                        ->getValues(),
                ],
            ),
            200,
            ['Content-Type' => 'text/javascript'],
        );

        $referer = $this->getReferer($request);
        if (null === $referer) {
            throw new MissingRefererException();
        }

        $this->websiteHitRepository->saveFromRawData(
            $website->id->toRfc4122(),
            $request->getClientIp(),
            $referer,
        );
        $this->apiBypasser->save($fingerPrint, $response);

        return $response;
    }
}

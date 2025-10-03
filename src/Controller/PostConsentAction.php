<?php

declare(strict_types=1);

namespace App\Controller;

use App\Cache\ApiBypasser;
use App\Cache\Fingerprint;
use App\Cache\WebsiteIdProvider;
use App\Entity\Consent;
use App\Repository\ConsentRepository;
use App\Repository\WebsiteRepository;
use App\Util\CorsController;
use App\Util\WebsiteViaReferrerController;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/consent', name: 'post_consent', methods: ['POST'])]
final readonly class PostConsentAction
{
    use CorsController;
    use WebsiteViaReferrerController;

    public function __construct(
        private ConsentRepository $consentRepository,
        private WebsiteRepository $websiteRepository,
        private ApiBypasser $apiBypasser,
        private WebsiteIdProvider $websiteIdProvider,
    ) {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(Request $request): Response
    {
        $dnt = '1' === $request->headers->get('dnt');
        $refererHost = $this->getRefererHost($request);
        $websiteId = $this->websiteIdProvider->get($refererHost, $request->getHost());
        $website = $this->websiteRepository->find($websiteId);
        $fingerPrint = Fingerprint::create($request, $refererHost, $websiteId, $dnt);

        $this->apiBypasser->remove($fingerPrint);

        $response = new Response(null, 204);
        $this->appendCorsHeaders($request, $response);

        /** @var string|null $userId */
        $userId = $request->request->get('user_id');
        if (!$userId) {
            throw new BadRequestException('Missing user_id');
        }

        try {
            $consent = $this->consentRepository->findOneByWebsiteAndUserId($website, $userId);
        } catch (NoResultException) {
            $consent = new Consent($website, $userId);
            $this->consentRepository->save($consent);
        }

        $acceptedTrackers = $request->request->all('tracker');
        foreach ($website->getTrackers() as $tracker) {
            $consent->setTrackerConsent($tracker->getId()->toRfc4122(), '1' === ($acceptedTrackers[$tracker->getId()->toRfc4122()] ?? false));
        }

        $consent->touch();
        $this->consentRepository->update($consent);

        $response->headers->setCookie(
            Cookie::create(
                $website->getId()->toRfc4122(),
                $userId,
                expire: new \DateTimeImmutable('+2 years'),
                secure: true,
                sameSite: Cookie::SAMESITE_NONE,
            ),
        );

        return $response;
    }
}

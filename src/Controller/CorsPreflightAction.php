<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\WebsiteRepository;
use App\Util\CorsController;
use App\Util\WebsiteViaReferrerController;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/consent', name: 'cors_preflight', methods: ['OPTIONS'])]
final readonly class CorsPreflightAction
{
    use WebsiteViaReferrerController;
    use CorsController;

    public function __construct(private WebsiteRepository $websiteRepository)
    {
    }

    /**
     * @throws NonUniqueResultException
     */
    public function __invoke(Request $request): Response
    {
        $this->getWebsiteWithRequest($this->websiteRepository, $request);

        $response = new Response(null, 204);
        $this->appendCorsHeaders($request, $response);

        return $response;
    }
}

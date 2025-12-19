<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[AsController]
#[Route('/admin', host: '%main_domain%')]
final readonly class SecurityController
{
    public function __construct(
        private AuthenticationUtils $authenticationUtils,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    #[Route(path: '/login', name: 'admin_login')]
    #[Template('admin/security/login.html.twig')]
    public function login(): array
    {
        return [
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
            'forgot_password_path' => $this->urlGenerator->generate('admin_forgot_password_request'),
            'forgot_password_enabled' => true,
            'remember_me_enabled' => true,
        ];
    }
}

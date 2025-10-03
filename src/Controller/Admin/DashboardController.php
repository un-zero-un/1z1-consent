<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin', host: '%main_domain%')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect($adminUrlGenerator->setController(WebsiteCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('1z1 Consent');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');

        yield MenuItem::section('Clients & Sites');
        yield MenuItem::linkToCrud('Clients', 'fa fa-building', ClientCrudController::getEntityFqcn());
        yield MenuItem::linkToCrud('Sites', 'fa fa-globe', WebsiteCrudController::getEntityFqcn());

        yield MenuItem::section('RGPD');
        yield MenuItem::linkToCrud('Traitements', 'fa fa-list-check', GDPRTreatmentController::getEntityFqcn());

        yield MenuItem::section('Statistiques');
        yield MenuItem::linkToCrud('Consentements', 'fa fa-handshake', ConsentCrudController::getEntityFqcn());
        yield MenuItem::linkToCrud('Page vues', 'fa fa-gauge', WebsiteHitCrudController::getEntityFqcn());

        yield MenuItem::section('Administration');
        yield MenuItem::linkToCrud('Agences', 'fa fa-building', AgencyCrudController::getEntityFqcn())->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToCrud('Serveurs', 'fa fa-server', ServerCrudController::getEntityFqcn());
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', AdminUserCrudController::getEntityFqcn())->setPermission('ROLE_ADMIN');
    }
}

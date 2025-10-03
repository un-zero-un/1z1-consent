<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\WebsiteDomain;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class WebsiteDomainController extends AbstractCrudController
{
    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('domain', 'Domaine');
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
                     ->disable(Action::NEW)
                     ->disable(Action::EDIT)
                     ->disable(Action::DETAIL)
                     ->disable(Action::INDEX)
                     ->disable(Action::DELETE);
    }

    public static function getEntityFqcn(): string
    {
        return WebsiteDomain::class;
    }
}

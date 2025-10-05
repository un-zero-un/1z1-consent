<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Person;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PersonCrudController extends AbstractCrudController
{
    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('État civil');
        yield TextField::new('firstName', 'Prénom');
        yield TextField::new('lastName', 'Nom');

        yield FormField::addPanel('Coordonnées');

        yield TextField::new('address', 'Adresse');
        yield TextField::new('postCode', 'Code Postal');
        yield TextField::new('city', 'Ville');
        yield CountryField::new('country', 'Pays');
        yield TextField::new('email', 'Adresse e-mail');
        yield TextField::new('phoneNumber', 'Numéro de téléphone');
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
                     ->disable(Action::NEW)
                     ->disable(Action::EDIT)
                     ->disable(Action::INDEX)
                     ->disable(Action::DELETE);
    }

    public static function getEntityFqcn(): string
    {
        return Person::class;
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\TreatmentOutOfEUTransfer;
use App\ValueObject\WarrantyType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class TreatmentOutOfEUTransferController extends AbstractCrudController
{
    public function configureFields(string $pageName): iterable
    {
        yield TextareaField::new('recipient', 'Destinataire');
        yield CountryField::new('country', 'Pays');
        yield ChoiceField::new('warrantyType', 'Type de garantie')
                   ->setChoices(['' => WarrantyType::cases()])
                   ->setFormType(EnumType::class)
                   ->setFormTypeOption('class', WarrantyType::class)
                   ->setFormTypeOption('choice_label', 'value')
                   ->onlyOnForms();
        yield TextareaField::new('documentationLink', 'Liens vers la documentation');
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
        return TreatmentOutOfEUTransfer::class;
    }
}

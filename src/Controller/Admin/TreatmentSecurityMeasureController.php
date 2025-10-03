<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\TreatmentSecurityMeasure;
use App\ValueObject\SecurityMeasure;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class TreatmentSecurityMeasureController extends AbstractCrudController
{
    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new('securityMeasure', 'Mesures de sécurité')
                   ->setChoices(['' => SecurityMeasure::cases()])
                   ->setFormType(EnumType::class)
                   ->setFormTypeOption('class', SecurityMeasure::class)
                   ->setFormTypeOption('choice_label', 'value')
                   ->onlyOnForms();

        yield TextareaField::new('details', 'Détails');
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
        return TreatmentSecurityMeasure::class;
    }
}

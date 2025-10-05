<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\TreatmentRecipientType;
use App\ValueObject\RecipientType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class TreatmentRecipientTypeController extends AbstractCrudController
{
    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new('recipientType', 'Type de destinataire')
                   ->setChoices(['' => RecipientType::cases()])
                   ->setFormType(EnumType::class)
                   ->setFormTypeOption('class', RecipientType::class)
                   ->setFormTypeOption('choice_label', 'value')
                   ->onlyOnForms();

        yield TextareaField::new('details', 'Détails');
    }

    #[\Override]
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
        return TreatmentRecipientType::class;
    }
}

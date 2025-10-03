<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\TreatmentConcernedPersonCategory;
use App\ValueObject\PersonCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class TreatmentConcernedPersonCategoriesController extends AbstractCrudController
{
    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new('personCategory', 'Catégorie de personnes')
                   ->setChoices(['' => PersonCategory::cases()])
                   ->setFormType(EnumType::class)
                   ->setFormTypeOption('class', PersonCategory::class)
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
        return TreatmentConcernedPersonCategory::class;
    }
}

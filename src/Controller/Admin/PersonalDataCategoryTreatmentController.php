<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\PersonalDataTreatmentCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class PersonalDataCategoryTreatmentController extends AbstractCrudController
{
    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('category', 'Catégorie');
        yield TextareaField::new('description', 'Description');
        yield TextareaField::new('duration', 'Durée de conservation');
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
        return PersonalDataTreatmentCategory::class;
    }
}

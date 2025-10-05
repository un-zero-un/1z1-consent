<?php

namespace App\Controller\Admin;

use App\Admin\Field\MonacoEditorField;
use App\Entity\Tracker;
use App\ValueObject\TrackerType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class TrackerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tracker::class;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom')->setColumns('col-md-12'),
            ChoiceField::new('type', 'Type de tracker')
                       ->setChoices(['' => TrackerType::cases()])
                       ->setFormType(EnumType::class)
                       ->setFormTypeOption('class', TrackerType::class)
                       ->setFormTypeOption('choice_label', 'label')
                       ->onlyOnForms()
                       ->setColumns('col-md-12'),
            TextField::new('trackerId', 'Identifiant de suivi (ex G-XXXXXX)')->setColumns('col-md-12'),
            UrlField::new('customUrl', 'URL personnalisée')->onlyOnForms()->setColumns('col-md-12'),
            BooleanField::new('useDefaultSnippet', 'Utiliser le code par défaut')->onlyOnForms()->setColumns('col-md-12'),
            MonacoEditorField::new('customCode', 'Code personnalisé')
                             ->onlyOnForms()
                             ->setColumns('col-md-12')
                             ->setFormTypeOption(
                                 'help',
                                 'Le code personnalisé sera utilisé à la place ou en complément du code par défaut. '.
                                 'les variables du trackers sont accessibles, ainsi que l\'object "tracker"'
                             ),
        ];
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
}

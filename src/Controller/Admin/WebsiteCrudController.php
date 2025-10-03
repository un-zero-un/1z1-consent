<?php

namespace App\Controller\Admin;

use App\Admin\Field\MonacoEditorField;
use App\Entity\Website;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class WebsiteCrudController extends AbstractCrudController
{
    use AgencyAwareCrudController;

    public function __construct(private readonly AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Website::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $defaultVariables = file_get_contents(realpath(__DIR__.'/../../../assets/dialog/variables.scss.txt'));
        $agency = $this->getAgency();

        return [
            IdField::new('id')->hideOnForm(),

            AssociationField::new('client', 'Client')
                            ->setQueryBuilder(
                                fn (QueryBuilder $queryBuilder) => $queryBuilder
                                    ->andWhere('entity.agency = :agency')
                                    ->setParameter('agency', $agency)
                            ),

            AssociationField::new('server', 'Serveur')
                            ->setQueryBuilder(
                                fn (QueryBuilder $queryBuilder) => $queryBuilder
                                    ->andWhere('entity.agency = :agency')
                                    ->setParameter('agency', $agency)
                            ),

            BooleanField::new('respectDoNotTrack', 'Respecter le "Do Not Track"')->hideOnIndex(),
            BooleanField::new('showOpenButton', 'Afficher le bouton d\'ouverture de la popup')->hideOnIndex(),
            BooleanField::new('addAccessLogToGDPR', 'Ajouter le journal d\'accès au registre')->hideOnIndex(),
            BooleanField::new('addTrackerToGDPR', 'Ajouter les trackers au registre')->hideOnIndex(),

            TextField::new('dialogTitle', 'Titre de la boite de dialogue')
                     ->setFormTypeOption('attr', ['placeholder' => 'Hello, on a besoin de votre permission'])
                     ->hideOnIndex(),
            TextareaField::new('dialogText', 'Texte de la boite de dialogue')
                         ->setFormTypeOption('attr', ['placeholder' => '<p>On aimerait utiliser des cookies pour améliorer votre expérience sur notre site.</p><p>Vous nous donnez votre autorisation ? Quelle que soit votre réponse, on ne vous embêtera plus avec cette question 🙂.</p>'])
                         ->hideOnIndex(),

            MonacoEditorField::new('customCss', 'CSS personnalisé', ['language' => 'css', 'attrs' => ['placeholder' => $defaultVariables]])
                             ->onlyOnForms()
                             ->hideOnIndex(),
            CollectionField::new('domains', 'Domaines')
                           ->allowAdd()
                           ->allowDelete()
                           ->renderExpanded()
                           ->useEntryCrudForm()
                           ->setTemplatePath('admin/fields/domains.html.twig'),
            CollectionField::new('trackers')
                           ->allowAdd()
                           ->allowDelete()
                           ->setEntryIsComplex()
                           ->useEntryCrudForm()
                           ->renderExpanded()
                           ->setColumns('col-md-9'),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Mis à jour le')->hideOnForm(),
            CollectionField::new('analytics', 'Statistiques')->setTemplatePath('admin/website/_analytics.html.twig')->onlyOnDetail(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
                     ->setEntityLabelInSingular('Site')
                     ->setEntityLabelInPlural('Sites')
                     ->setDefaultSort(['client.name' => 'ASC'])
                     ->setEntityPermission('IS_OWNER');
    }

    public function configureAssets(Assets $assets): Assets
    {
        return parent::configureAssets($assets)
                     ->addWebpackEncoreEntry('admin_editor')
                     ->addWebpackEncoreEntry('stimulus');
    }

    public function configureActions(Actions $actions): Actions
    {
        $showConsents = Action::new('showConsents', 'Consentements')
                              ->linkToUrl(
                                  fn (Website $website) => $this->adminUrlGenerator
                                      ->setController(ConsentCrudController::class)
                                      ->setAction(Action::INDEX)
                                      ->set('filters[website][comparison]', '=')
                                      ->set('filters[website][value]', $website->getId())
                                      ->generateUrl(),
                              );

        return parent::configureActions($actions)
                     ->add(Action::INDEX, $showConsents)
                     ->add(Action::INDEX, Action::DETAIL);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $agency = $this->getAgency();

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
                     ->andWhere('client.agency = :agency')
                     ->setParameter('agency', $agency);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\GDPRTreatment;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Doctrine\Types\UuidType;

/**
 * @extends AbstractCrudController<GDPRTreatment>
 */
final class GDPRTreatmentController extends AbstractCrudController
{
    use AgencyAwareCrudController;

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        $agency = $this->getAgency();

        yield AssociationField::new('client', 'Client')->setQueryBuilder(
            fn (QueryBuilder $queryBuilder): QueryBuilder => $queryBuilder
                    ->innerJoin('entity.agency', 'agency')
                    ->andWhere('agency.id = :agency_id')
                    ->setParameter('agency_id', $agency->getId(), UuidType::NAME),
        );

        yield IntegerField::new('ref', 'N° / RÉF');
        yield TextField::new('name', 'Nom du traitement');

        yield FormField::addPanel('Finalité(s) du traitement effectué');

        yield TextField::new('processingPurpose', 'Finalité principale');
        yield TextField::new('processingSubPurpose1', 'Sous-finalité 1')->hideOnIndex();
        yield TextField::new('processingSubPurpose2', 'Sous-finalité 2')->hideOnIndex();
        yield TextField::new('processingSubPurpose3', 'Sous-finalité 3')->hideOnIndex();
        yield TextField::new('processingSubPurpose4', 'Sous-finalité 4')->hideOnIndex();
        yield TextField::new('processingSubPurpose5', 'Sous-finalité 5')->hideOnIndex();

        yield FormField::addPanel('Catégories de données');
        yield CollectionField::new('personalDataCategoryTreatments', 'Catégories de données personnelles concernées')
                             ->allowAdd()
                             ->allowDelete()
                             ->setEntryIsComplex()
                             ->useEntryCrudForm()
                             ->hideOnIndex();

        yield CollectionField::new('sensitiveDataCategoryTreatments', 'Données sensibles')
                             ->allowAdd()
                             ->allowDelete()
                             ->setEntryIsComplex()
                             ->useEntryCrudForm()
                             ->hideOnIndex();

        yield FormField::addPanel('Personnes / Échanges');
        yield CollectionField::new('concernedPersonCategories', 'Catégories de personnes concernées')
                             ->allowAdd()
                             ->allowDelete()
                             ->setEntryIsComplex()
                             ->useEntryCrudForm()
                             ->hideOnIndex();
        yield CollectionField::new('recipientTypes', 'Destinataires')
                             ->allowAdd()
                             ->allowDelete()
                             ->setEntryIsComplex()
                             ->useEntryCrudForm()
                             ->hideOnIndex();

        yield FormField::addPanel('Sécurité');
        yield CollectionField::new('securityMeasures', 'Mesures de sécurité')
                             ->allowAdd()
                             ->allowDelete()
                             ->setEntryIsComplex()
                             ->useEntryCrudForm()
                             ->hideOnIndex();
        yield CollectionField::new('outOfEUTransfers', '̂Transferts hors UE')
                             ->allowAdd()
                             ->allowDelete()
                             ->setEntryIsComplex()
                             ->useEntryCrudForm()
                             ->hideOnIndex();

        yield DateTimeField::new('createdAt', 'Créé le')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Mis à jour le')->hideOnForm();
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
                     ->setEntityLabelInSingular('Traitement (RGPD)')
                     ->setEntityLabelInPlural('Traitements (RGPD)')
                     ->setDefaultSort(['client.name' => 'ASC', 'ref' => 'ASC'])
                     ->setEntityPermission('IS_OWNER');
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
                     ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    #[\Override]
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $agency = $this->getAgency();

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->innerJoin('client.agency', 'agency')
            ->andWhere('agency.id = :agency_id')
            ->setParameter('agency_id', $agency->getId(), UuidType::NAME);
    }

    #[\Override]
    public static function getEntityFqcn(): string
    {
        return GDPRTreatment::class;
    }
}

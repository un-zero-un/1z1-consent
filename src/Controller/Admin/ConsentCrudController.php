<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\Filter\ConsentStatusFilter;
use App\Entity\Consent;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ConsentCrudController extends AbstractCrudController
{
    use AgencyAwareCrudController;

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Consentement')
            ->setEntityLabelInPlural('Consentements')
            ->setDefaultSort(['updatedAt' => 'DESC'])
            ->setEntityPermission('IS_OWNER');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID');

        yield AssociationField::new('website', 'Site');
        yield TextField::new('userId', 'Identifiant utilisateur');

        yield DateTimeField::new('createdAt', 'Recueilli le');
        yield DateTimeField::new('updatedAt', 'Mis à jour le');

        yield CollectionField::new('trackerConsents', 'Consentements')
            ->setTemplatePath('admin/consent/trackerConsents.html.twig');
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->disable(Action::DELETE, Action::EDIT, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add('website')
            ->add(ConsentStatusFilter::new('status', 'État du consentement'));
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $agency = $this->getAgency();

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->innerJoin('entity.website', 'website')
            ->innerJoin('website.client', 'client')
            ->andWhere('client.agency = :agency')
            ->setParameter('agency', $agency);
    }

    public static function getEntityFqcn(): string
    {
        return Consent::class;
    }
}

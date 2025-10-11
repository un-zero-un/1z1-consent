<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\WebsiteHit;
use Doctrine\ORM\EntityRepository;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Bridge\Doctrine\Types\UuidType;

/**
 * @extends AbstractCrudController<WebsiteHit>
 */
final class WebsiteHitCrudController extends AbstractCrudController
{
    use AgencyAwareCrudController;

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Page vue')
            ->setEntityLabelInPlural('Page vues')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setEntityPermission('IS_OWNER');
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID');
        yield DateTimeField::new('createdAt', 'Date');
        yield AssociationField::new('website', 'Site');
        yield TextField::new('ipAddress', 'IP');
        yield TextField::new('referer', 'URL référente');
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->disable(Action::DELETE, Action::EDIT, Action::NEW, Action::DETAIL);
    }

    #[\Override]
    public function configureFilters(Filters $filters): Filters
    {
        $agency = $this->getAgency();

        return parent::configureFilters($filters)
            ->add(
                EntityFilter::new('website')
                    ->setFormTypeOption('value_type_options.query_builder', fn (EntityRepository $er) => $er->createQueryBuilder('w')
                        ->innerJoin('w.client', 'c')
                        ->innerJoin('c.agency', 'agency')
                        ->andWhere('agency.id = :agency_id')
                        ->setParameter('agency_id', $agency->id, UuidType::NAME)),
            );
    }

    #[\Override]
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $agency = $this->getAgency();

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->innerJoin('entity.website', 'website')
            ->innerJoin('website.client', 'client')
            ->innerJoin('client.agency', 'agency')
            ->andWhere('agency.id = :agency_id')
            ->setParameter('agency_id', $agency->id, UuidType::NAME);
    }

    #[\Override]
    public static function getEntityFqcn(): string
    {
        return WebsiteHit::class;
    }
}

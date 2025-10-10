<?php

namespace App\Controller\Admin;

use App\Entity\Server;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Doctrine\Types\UuidType;

/**
 * @extends AbstractCrudController<Server>
 */
final class ServerCrudController extends AbstractCrudController
{
    use AgencyAwareCrudController;

    #[\Override]
    public static function getEntityFqcn(): string
    {
        return Server::class;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom'),
            IntegerField::new('quantityOfCO2eqPerYear', 'Quantité de CO2eq par an')->setHelp('kgCO2eq/an'),
            IntegerField::new('numberOfUnmanagedSites', 'Nombre de sites non gérés')->hideOnIndex(),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Mis à jour le')->hideOnForm(),
        ];
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Serveur')
            ->setEntityLabelInPlural('Serveurs')
            ->setDefaultSort(['name' => 'ASC'])
            ->setEntityPermission('IS_OWNER');
    }

    #[\Override]
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $agency = $this->getAgency();

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->innerJoin('entity.agency', 'agency')
            ->andWhere('agency.id = :agency_id')
            ->setParameter('agency_id', $agency->getId(), UuidType::NAME);
    }
}

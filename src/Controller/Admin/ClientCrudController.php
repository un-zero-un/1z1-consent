<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use Doctrine\ORM\QueryBuilder;
use Dompdf\Dompdf;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ClientCrudController extends AbstractCrudController
{
    use AgencyAwareCrudController;

    public function __construct()
    {
    }

    public static function getEntityFqcn(): string
    {
        return Client::class;
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        $agency = $this->getAgency();

        $agencyQueryBuilder = fn (QueryBuilder $queryBuilder): QueryBuilder => $queryBuilder
                    ->innerJoin('entity.client', 'client')
                    ->andWhere('client.agency = :agency')
                    ->setParameter('agency', $agency);

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom'),
            AssociationField::new('dataResponsible', 'Responsable des données')->setQueryBuilder($agencyQueryBuilder),
            AssociationField::new('dpo', 'DPO')->setQueryBuilder($agencyQueryBuilder),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Mis à jour le')->hideOnForm(),
            CollectionField::new('persons', 'Personnes')
                ->allowAdd()
                ->allowDelete()
                ->setEntryIsComplex()
                ->useEntryCrudForm(),
        ];
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Client')
            ->setEntityLabelInPlural('Clients')
            ->setDefaultSort(['name' => 'ASC'])
            ->setEntityPermission('IS_OWNER');
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        $viewRegister = Action::new('viewRegister', 'Voir le registre RGPD')
            ->linkToCrudAction('viewRegister');

        $viewRegisterPDF = Action::new('viewPDFRegister', 'Voir le registre RGPD (PDF)')
            ->linkToCrudAction('viewPDFRegister');

        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $viewRegister)
            ->add(Crud::PAGE_DETAIL, $viewRegister)
            ->add(Crud::PAGE_INDEX, $viewRegisterPDF)
            ->add(Crud::PAGE_DETAIL, $viewRegisterPDF);
    }

    #[\Override]
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $agency = $this->getAgency();

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.agency = :agency')
            ->setParameter('agency', $agency);
    }

    #[Template('admin/client/viewRegister.html.twig')]
    public function viewRegister(AdminContext $adminContext): array
    {
        return ['client' => $adminContext->getEntity()->getInstance()];
    }

    public function viewPDFRegister(AdminContext $adminContext): Response
    {
        $client = $adminContext->getEntity()->getInstance();

        return new StreamedResponse(
            function () use ($client): void {
                $dompdf = new Dompdf();
                $dompdf->loadHtml($this->renderView('admin/client/viewPDFRegister.html.twig', ['client' => $client]));
                $dompdf->setPaper('A4');
                $dompdf->render();

                echo $dompdf->output();
                flush();
            },
            201,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="registre_rgpd_'.$client->getName().'.pdf"',
            ]
        );
    }
}

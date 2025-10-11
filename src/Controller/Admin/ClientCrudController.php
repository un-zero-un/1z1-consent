<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Entity\Person;
use App\Generator\GDPRWebsiteTreatmentGenerator;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Dompdf\Dompdf;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
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
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @extends AbstractCrudController<Client>
 */
final class ClientCrudController extends AbstractCrudController
{
    use AgencyAwareCrudController;

    public function __construct(
        private readonly GDPRWebsiteTreatmentGenerator $treatmentGenerator,
    ) {
    }

    #[\Override]
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
            ->innerJoin('client.agency', 'agency')
            ->andWhere('agency.id = :agency_id')
            ->setParameter('agency_id', $agency->id, UuidType::NAME);

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom'),
            AssociationField::new('dataResponsible', 'Responsable des données')->setQueryBuilder($agencyQueryBuilder),
            AssociationField::new('dpo', 'DPO')->setQueryBuilder($agencyQueryBuilder),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Mis à jour le')->hideOnForm(),
            CollectionField::new('persons', 'Personnes')
                ->renderExpanded()
                ->showEntryLabel()
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
            ->setIcon('fa fa-file')
            ->linkToCrudAction('viewRegister');

        $viewRegisterPDF = Action::new('viewPDFRegister', 'Voir le registre RGPD (PDF)')
            ->setIcon('fa fa-file-pdf')
            ->linkToCrudAction('viewPDFRegister');

        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $viewRegister)
            ->add(Crud::PAGE_DETAIL, $viewRegister)
            ->add(Crud::PAGE_INDEX, $viewRegisterPDF)
            ->add(Crud::PAGE_DETAIL, $viewRegisterPDF)
            ->update(Crud::PAGE_INDEX, Action::DETAIL, fn (Action $action): Action => $action->setIcon('fa fa-eye'));
    }

    #[\Override]
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $agency = $this->getAgency();

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->innerJoin('entity.agency', 'agency')
            ->andWhere('agency.id = :agency_id')
            ->setParameter('agency_id', $agency->id, UuidType::NAME);
    }

    #[\Override]
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Collection<int, Person> $persons */
        $persons = $entityInstance->persons;
        foreach ($persons as $person) {
            $entityInstance->removePerson($person);
            $entityManager->remove($person);
        }

        parent::deleteEntity($entityManager, $entityInstance);
    }

    #[AdminRoute('/{entityId}/view-register', name: 'viewRegister')]
    #[Template('admin/client/viewRegister.html.twig')]
    public function viewRegister(AdminContext $adminContext): array
    {
        $client = $adminContext->getEntity()->getInstance();
        assert($client instanceof Client);

        return [
            'client' => $client,
            'website_treatments' => $this->treatmentGenerator->generateForClient($client),
        ];
    }

    #[AdminRoute('/{entityId}/view-pdf-register', name: 'viewPDFRegister')]
    public function viewPDFRegister(AdminContext $adminContext): Response
    {
        $client = $adminContext->getEntity()->getInstance();
        if (!$client instanceof Client) {
            throw $this->createNotFoundException('Client not found');
        }

        return new StreamedResponse(
            function () use ($client): void {
                $dompdf = new Dompdf();
                $dompdf->loadHtml($this->renderView(
                    'admin/client/viewPDFRegister.html.twig',
                    [
                        'client' => $client,
                        'website_treatments' => $this->treatmentGenerator->generateForClient($client),
                    ]
                ));
                $dompdf->setPaper('A4');
                $dompdf->render();

                echo $dompdf->output();
                flush();
            },
            201,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="registre_rgpd_'.($client->name ?: '').'.pdf"',
            ]
        );
    }
}

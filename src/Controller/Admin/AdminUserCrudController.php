<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use Doctrine\ORM\QueryBuilder;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @extends AbstractCrudController<AdminUser>
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class AdminUserCrudController extends AbstractCrudController
{
    use AgencyAwareCrudController;

    public function __construct(
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly MailerInterface $mailer,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        #[Autowire('%env(EMAIL_FROM_ADDRESS)%')]
        private readonly string $emailFromAddress,
    ) {
    }

    #[\Override]
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield EmailField::new('email', 'Email');
        yield AssociationField::new('agency', 'Agence');
        yield DateTimeField::new('createdAt', 'Créé le')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Mis à jour le')->hideOnForm();
    }

    #[\Override]
    public function configureCrud(Crud $crud): Crud
    {
        $crud = parent::configureCrud($crud)
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs');

        if (!$this->isGranted('ROLE_ADMIN')) {
            $crud = $crud->setEntityPermission('IS_OWNER');
        }

        return $crud;
    }

    #[\Override]
    public function configureActions(Actions $actions): Actions
    {
        $sendResetPasswordLink = Action::new('sendResetPasswordLink', 'Envoyer un lien de réinitialisation')
            ->setIcon('fa fa-key')
            ->linkToCrudAction('sendResetPasswordLink');

        $actions = parent::configureActions($actions);

        if (!$this->isGranted('IS_IMPERSONATOR')) {
            $impersonateAction =
                Action::new('impersonate', 'Se connecter en tant que')
                    ->setIcon('fa fa-user-secret')
                    ->linkToUrl(fn (AdminUser $user) => $this->generateUrl('admin', ['_switch_user' => $user->getEmail()]));

            $actions = $actions
                ->add(Crud::PAGE_DETAIL, $impersonateAction)
                ->add(Crud::PAGE_INDEX, $impersonateAction);
        }

        return $actions
            ->add(Crud::PAGE_INDEX, $sendResetPasswordLink)
            ->add(Crud::PAGE_DETAIL, $sendResetPasswordLink);
    }

    #[\Override]
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $agency = $this->getAgency();

        if ($this->isGranted('ROLE_ADMIN')) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.agency = :agency')
            ->setParameter(
                'agency',
                $agency
            );
    }

    #[AdminRoute(path: '/{entityId}/send-reset-password-link', name: 'send_reset_password_link')]
    public function sendResetPasswordLink(AdminContext $adminContext): Response
    {
        $user = $adminContext->getEntity()->getInstance();
        if (!$user instanceof AdminUser) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $resetToken = $this->resetPasswordHelper->generateResetToken($user);

        $emailAddress = $user->getEmail();
        if (null === $emailAddress) {
            throw $this->createNotFoundException('L\'utilisateur n\'a pas d\'adresse email.');
        }

        $email = new TemplatedEmail()
            ->from(Address::create($this->emailFromAddress))
            ->to($emailAddress)
            ->subject('Réinitialisation de votre mot de passe')
            ->htmlTemplate('admin/reset_password/email.html.twig')
            ->context(['reset_token' => $resetToken]);

        $this->mailer->send($email);

        /** @var Session $session */
        $session = $adminContext->getRequest()->getSession();
        $session->getFlashBag()->add('success', 'Un email de réinitialisation de mot de passe a été envoyé à l\'utilisateur.');

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(AdminUserCrudController::class)
                ->setAction('index')
                ->generateUrl()
        );
    }

    #[\Override]
    public static function getEntityFqcn(): string
    {
        return AdminUser::class;
    }
}

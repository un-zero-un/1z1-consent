<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\AdminUser;
use App\Entity\Agency;
use App\Repository\AdminUserRepository;
use App\Repository\AgencyRepository;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand('admin:create-user')]
final readonly class CreateAdminUserCommand
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private AdminUserRepository $adminUserRepository,
        private AgencyRepository $agencyRepository,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'The user email', name: 'email')]
        ?string $email = null,
        #[Argument(description: 'The user password', name: 'password')]
        ?string $password = null,
        #[Argument(description: 'Agency to assign the user to', name: 'agency')]
        ?string $agency = null,
        #[Option(description: 'Create as admin', name: 'admin')]
        bool $asAdmin = false,
    ): int {
        while (!is_string($email)) {
            /** @var string|null $email */
            $email = $io->askQuestion(new Question('Email : '));
        }

        while (!is_string($password)) {
            /** @var string|null $password */
            $password = $io->askQuestion(new Question('Password : ')->setHidden(true)->setHiddenFallback(false));
        }

        while (!is_string($agency)) {
            /** @var string|null $agency */
            $agency = $io->askQuestion(new Question('Agency : '));
        }

        $agencyEntity = $this->agencyRepository->findOneByName($agency);
        if (!$agencyEntity instanceof Agency) {
            $io->error(sprintf('Agency "%s" not found', $agency));

            return Command::FAILURE;
        }

        $user = new AdminUser($email);
        $user->password = $this->passwordHasher->hashPassword($user, $password);
        $user->setAgency($agencyEntity);

        if ($asAdmin) {
            $user->roles = ['ROLE_ADMIN'];
        }
        $this->adminUserRepository->save($user);

        $io->success('Admin user created');

        return Command::SUCCESS;
    }
}

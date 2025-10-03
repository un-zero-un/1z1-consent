<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\AdminUser;
use App\Repository\AdminUserRepository;
use App\Repository\AgencyRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand('admin:create-user')]
final class CreateAdminUserCommand extends Command
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly AdminUserRepository $adminUserRepository,
        private readonly AgencyRepository $agencyRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'The user email')
            ->addArgument('password', InputArgument::OPTIONAL, 'The user password')
            ->addArgument('agency', InputArgument::OPTIONAL, 'Agency to assign the user to')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'Create as admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $email = new Question('Email : ', $input->getArgument('email'));
        $password = new Question('Password : ', $input->getArgument('password'));

        $user = new AdminUser($helper->ask($input, $output, $email));
        $user->setPassword($this->passwordHasher->hashPassword($user, $helper->ask($input, $output, $password)));
        $user->setAgency($this->agencyRepository->findOneByName($input->getArgument('agency')));

        if ($input->getOption('admin')) {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $this->adminUserRepository->save($user);

        $io->success('Admin user created');

        return self::SUCCESS;
    }
}

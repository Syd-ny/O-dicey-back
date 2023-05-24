<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OdiceyCreateUserCommand extends Command
{
    protected static $defaultName = 'odicey:create-user';
    protected static $defaultDescription = 'Create a new user for O\'Dicey';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        // Command dependencies
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to create a user!')
            ->addArgument('username', InputArgument::REQUIRED, 'Your email')
            ->addArgument('login', InputArgument::REQUIRED, 'Your login')
            ->addArgument('password', InputArgument::REQUIRED, 'Your password')
            ->addArgument('roles', InputArgument::REQUIRED, 'Your roles')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $login = $input->getArgument('login');
        $password = $input->getArgument('password');
        $roles = $input->getArgument('roles');

        $user = new User();

        if ($username) {
            $user->setEmail($username);
            $io->note(sprintf('You passed an email: %s', $username));
        }

        if ($login) {
            $user->setLogin($login);
            $io->note(sprintf('You passed a login: %s', $login));
        }

        if ($password) {
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $io->note(sprintf('You passed a password: %s', $password));
        }

        if ($roles) {
            $user->setRoles([$roles]);
            $io->note(sprintf('You passed a role: %s', $roles));
        }

        $this->entityManager->getRepository(User::class)->add($user, true);

        $io->success('You created a new User!');

        return Command::SUCCESS;
    }
}


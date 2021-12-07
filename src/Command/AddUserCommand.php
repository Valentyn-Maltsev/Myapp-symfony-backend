<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class AddUserCommand extends Command
{
    protected static $defaultName = 'app:add-user';
    protected static $defaultDescription = 'Add user';

    /**
     * @var UserRepository
     */
    private $userRepository;


    /**
     * @var PasswordHasherFactoryInterface
     */
    private $passwordHasher;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(string $name = null, UserRepository $userRepository, PasswordHasherFactoryInterface $passwordHasher,EntityManagerInterface $entityManager)
    {
        parent::__construct($name);
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', 'em',InputArgument::OPTIONAL, 'Email')
            ->addOption('password', 'p', InputArgument::OPTIONAL, 'Password')
            ->addOption('isAdmin', '',InputArgument::OPTIONAL, 'If set the user is created as an administrator', false)
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $stopwatch = new Stopwatch();
        $stopwatch->start('add-user-command');

        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $isAdmin = $input->getOption('isAdmin');

        $io->title('Add User Command Wizard');
        $io->text([
            'Please, enter some information'
        ]);

        if (!$email) {
            $email = $io->ask('Please enter your email');
        }

        if (!$password) {
            $password = $io->askHidden('Password (your type will be hidden)');
        }

        if (!$isAdmin) {
            $question = new Question('Is admin? (1 or 0)', 0);
            $isAdmin = $io->askQuestion($question);
        }

        $isAdmin = boolval($isAdmin);

        try {
            $user = $this->createUser($email, $password, $isAdmin);
        } catch (\RuntimeException $exception) {
            $io->comment($exception->getMessage());
            return Command::FAILURE;
        }


        $successMessage = sprintf('%s was successfully created: %s', $isAdmin ? 'Administrator' : 'User', 'Email');
        $io->success($successMessage);

        $event = $stopwatch->stop('add-user-command');
        $stopwatchMessage = sprintf('New user\'s id: %s / Elapsed time: %.2f s / Consumed memory: %.2f MB',
            $user->getId(),
            $event->getDuration() / 1000,
            $event->getMemory() / 1000 / 1000
        );

        $io->comment($stopwatchMessage);

        return Command::SUCCESS;
    }

    /**
     * @param string $email
     * @param string $password
     * @param bool $isAdmin
     * @param UserRepository $userRepository
     */
    private function createUser(string $email, string $password, bool $isAdmin)
    {
        $existingUser = $this->userRepository->findOneBy(['email' => $email]);
        if ($existingUser) {
            throw new RuntimeException('User already exist');
        }


        $user = new User();
        $user->setEmail($email)
            ->setRoles([$isAdmin ? 'ROLE_ADMIN' : 'ROLE_USER'])
            ->setIsVerified(true);

        $encodedPassword = $this->passwordHasher->getPasswordHasher($user)->hash($password);
        $user->setPassword($encodedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}

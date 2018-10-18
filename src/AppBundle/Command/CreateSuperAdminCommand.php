<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Command;

use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateSuperAdminCommand extends ContainerAwareCommand
{
    private $userManager;

    /**
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
        parent::__construct();
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this->setName('superadmin:create')
            ->setDescription('Create a new Super Admin')
        ;
    }

    /**
     * Execution of the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $output->writeln("<fg=white;bg=green;options=bold>Create a new Super Admin</>\r\n");

        $usernameQ = new Question('<options=bold>Username : </>', '');
        $username = $helper->ask($input, $output, $usernameQ);

        $lastnameQ = new Question('<options=bold>Lastname : </>', '');
        $lastname = $helper->ask($input, $output, $lastnameQ);

        $firstnameQ = new Question('<options=bold>Firstname : </>', '');
        $firstname = $helper->ask($input, $output, $firstnameQ);

        $phoneQ = new Question('<options=bold>Phone : </>', '');
        $phone = $helper->ask($input, $output, $phoneQ);

        $emailQ = new Question('<options=bold>Email : </>', '');
        $email = $helper->ask($input, $output, $emailQ);

        $plainPasswordQ = new Question('<options=bold>Password : </>', '');
        $plainPasswordQ->setHidden(true);
        $plainPassword = $helper->ask($input, $output, $plainPasswordQ);

        $user = $this->userManager->createUser();
        $user->setUsername($username);
        $user->setUsernameCanonical($username);
        $user->setLastName($lastname);
        $user->setFirstName($firstname);
        $user->setPhone($phone);
        $user->setEmail($email);
        $user->setEmailCanonical($email);
        $user->setEnabled(1);
        $user->setPlainPassword($plainPassword);
        $user->setRoles(['ROLE_SUPER_ADMIN']);

        $output->writeln("\r\n<fg=white;bg=green;options=bold>A new Super Admin has been created !</>");

        $this->userManager->updateUser($user);
    }
}

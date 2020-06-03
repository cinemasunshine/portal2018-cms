<?php

/**
 * EncryptPasswordCommand.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Cinemasunshine\PortalAdmin\Console\Command\AdminUser;

use Cinemasunshine\PortalAdmin\Console\Command\BaseCommand;
use Cinemasunshine\PortalAdmin\ORM\Entity\AdminUser;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AdminUser encrypt password command.
 */
class EncryptPasswordCommand extends BaseCommand
{
    /**
     * @inheritDoc
     */
    protected static $defaultName = 'admin-user:encrypt-psw';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        // Description & Help
        $this->setDescription('Password encrypt for admin user.');

        // Argument & option
        $this->addArgument('password', InputArgument::REQUIRED, 'Plain text.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $password = $input->getArgument('password');
        $output->writeln('plain: ' . $password);

        $encryptedPassword = $this->encryptPassword($password);
        $output->writeln('encrypted: ' . $encryptedPassword);

        return 0;
    }

    /**
     * encrypt password
     *
     * @param string $password plain text
     * @return string encrypted password
     */
    protected function encryptPassword(string $password): string
    {
        return AdminUser::encryptPassword($password);
    }
}

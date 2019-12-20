<?php
/**
 * EncryptPasswordCommand.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace Cinemasunshine\PortalAdmin\Console\Command\AdminUser;

use Cinemasunshine\PortalAdmin\Console\Command\BaseCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AdminUser encrypt password command.
 */
final class EncryptPasswordCommand extends BaseCommand
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
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('TODO');
    }
}

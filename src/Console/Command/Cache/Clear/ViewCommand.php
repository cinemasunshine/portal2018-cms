<?php

/**
 * ViewCommand.php
 */

declare(strict_types=1);

namespace App\Console\Command\Cache\Clear;

use App\Console\Command\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Slim\Views\Twig;
use Twig\Cache\CacheInterface;
use Twig\Cache\FilesystemCache;
use Twig\Cache\NullCache;

/**
 * Clear view cache command.
 */
class ViewCommand extends BaseCommand
{
    protected static $defaultName = 'cache:clear:view';

    /** @var Twig */
    protected $view;

    /**
     * construct
     *
     * @param Twig $view
     */
    public function __construct(Twig $view)
    {
        $this->view = $view;

        parent::__construct();
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var CacheInterface $cache */
        $cache = $this->view->getEnvironment()->getCache(false);

        if ($cache instanceof FilesystemCache) {
            // FilesystemCacheからはディレクトリを取得できないので
            /** @var string $cacheDir */
            $cacheDir = $this->view->getEnvironment()->getCache();
            $this->clearFilesystemCache($cacheDir, $output);
        } elseif ($cache instanceof NullCache) {
            $output->writeln('Disable cache.');
        } else {
            throw new \RuntimeException(sprintf('This cache interface is not supported (%s).', get_class($cache)));
        }

        $output->writeln('Command exit.');

        return 0;
    }

    /**
     * clear filesystem cache
     *
     * @param string          $dir
     * @param OutputInterface $output
     * @return void
     */
    protected function clearFilesystemCache(string $dir, OutputInterface $output)
    {
        $output->writeln('Clear filesystem chace.');

        $filesystem = new Filesystem();
        $filesystem->remove($dir);

        $output->writeln(sprintf('dir- %s', $dir));
    }
}

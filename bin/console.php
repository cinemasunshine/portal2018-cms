<?php
/**
 * console.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 * @link https://github.com/symfony/console
 */

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Console\Application;

use Cinemasunshine\PortalAdmin\Console\Command\{
    Cache\Clear\ViewCommand as CacheClearViewCommand
};

/** @var \Slim\App $app */
$app = require dirname(__DIR__) . '/src/bootstrap.php';
$container = $app->getContainer();

$application = new Application();

// register commands
$application->add(new CacheClearViewCommand($container->get('view')));


$application->run();

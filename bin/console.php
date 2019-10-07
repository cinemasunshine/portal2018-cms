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

$application = new Application();

// register commands


$application->run();

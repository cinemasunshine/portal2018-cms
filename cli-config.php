<?php
/**
 * cli-config.php
 * 
 * Setting up the Doctrine Commandline Tool
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 * @link https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/configuration.html#setting-up-the-commandline-tool
 */

use Doctrine\ORM\Tools\Console\ConsoleRunner;

/** @var \Slim\App $app */
$app = require './src/bootstrap.php';

$container = $app->getContainer();

return ConsoleRunner::createHelperSet($container->get('em'));

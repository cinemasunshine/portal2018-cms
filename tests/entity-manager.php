<?php

declare(strict_types=1);

/**
 * entity-manager.php
 *
 * @link https://github.com/phpstan/phpstan-doctrine
 */

use Slim\App as SlimApp;

/** @var SlimApp $app */
$app = require dirname(__DIR__) . '/src/bootstrap.php';

return $app->getContainer()->get('em');

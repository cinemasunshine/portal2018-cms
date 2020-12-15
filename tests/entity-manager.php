<?php

/**
 * entity-manager.php
 *
 * @link https://github.com/phpstan/phpstan-doctrine
 */

/** @var \Slim\App $app */
$app = require dirname(__DIR__) . '/src/bootstrap.php';

return $app->getContainer()->get('em');

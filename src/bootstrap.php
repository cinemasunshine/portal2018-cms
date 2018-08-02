<?php
/**
 * index.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 * 
 * @return \Slim\App
 */

define('APP_ROOT', realpath(dirname(__DIR__)));

require_once APP_ROOT . '/vendor/autoload.php';

/** @var array $settings */
$settings = require APP_ROOT . '/config/settings.php';

$app = new \Slim\App(['settings' => $settings]);

// Set up dependencies
require APP_ROOT . '/config/container.php';

// Register routes
require APP_ROOT . '/config/routes.php';

return $app;

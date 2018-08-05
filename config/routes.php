<?php
/**
 * routes.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

use Cinemasunshine\PortalAdmin\Controller\AuthController;
use Cinemasunshine\PortalAdmin\Controller\IndexController;

$app->get('/login', AuthController::class . ':login')->setName('login');

$app->get('/', IndexController::class . ':index')->setName('homepage');

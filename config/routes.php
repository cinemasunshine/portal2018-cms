<?php
/**
 * routes.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

use Cinemasunshine\PortalAdmin\Controller\AuthController;
use Cinemasunshine\PortalAdmin\Controller\IndexController;

use Cinemasunshine\PortalAdmin\Middleware\AuthMiddleware;

$app->get('/login', AuthController::class . ':login')->setName('login');
$app->post('/auth', AuthController::class . ':auth')->setName('auth');

$app->group('', function () {
    $this->get('/', IndexController::class . ':index')->setName('homepage');
})->add(new AuthMiddleware($container));

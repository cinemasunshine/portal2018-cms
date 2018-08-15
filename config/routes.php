<?php
/**
 * routes.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

use Cinemasunshine\PortalAdmin\Controller\AuthController;
use Cinemasunshine\PortalAdmin\Controller\CampaignController;
use Cinemasunshine\PortalAdmin\Controller\IndexController;
use Cinemasunshine\PortalAdmin\Controller\TitleController;

use Cinemasunshine\PortalAdmin\Middleware\AuthMiddleware;

$app->get('/login', AuthController::class . ':login')->setName('login');
$app->post('/auth', AuthController::class . ':auth')->setName('auth');
$app->get('/logout', AuthController::class . ':logout')->setName('logout');

$app->group('', function () {
    $this->get('/', IndexController::class . ':index')->setName('homepage');
    
    $this->group('/title', function() {
        $this->get('/list', TitleController::class . ':list')->setName('title_list');
        $this->get('/new', TitleController::class . ':new')->setName('title_new');
        $this->post('/create', TitleController::class . ':create')->setName('title_create');
        $this->get('/{id}/edit', TitleController::class . ':edit')->setName('title_edit');
        $this->post('/{id}/update', TitleController::class . ':update')->setName('title_update');
        $this->get('/{id}/delete', TitleController::class . ':delete')->setName('title_delete');
    });
    
    $this->group('/campaign', function() {
        $this->get('/new', CampaignController::class . ':new')->setName('campaign_new');
    });
})->add(new AuthMiddleware($container));

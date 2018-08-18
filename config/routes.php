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

use Cinemasunshine\PortalAdmin\Controller\API\CampaignController as CampaignApiController;
use Cinemasunshine\PortalAdmin\Controller\API\TitleController as TitleApiController;

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
        $this->get('/list', CampaignController::class . ':list')->setName('campaign_list');
        $this->get('/new', CampaignController::class . ':new')->setName('campaign_new');
        $this->post('/create', CampaignController::class . ':create')->setName('campaign_create');
        $this->get('/{id}/edit', CampaignController::class . ':edit')->setName('campaign_edit');
        $this->post('/{id}/update', CampaignController::class . ':update')->setName('campaign_update');
        $this->get('/{id}/delete', CampaignController::class . ':delete')->setName('campaign_delete');
    });
    
    $this->group('/api', function() {
        $this->group('/title', function() {
            $this->get('/list', TitleApiController::class . ':list');
        });
        
        $this->group('/campaign', function() {
            $this->get('/list', CampaignApiController::class . ':list');
        });
    });
})->add(new AuthMiddleware($container));

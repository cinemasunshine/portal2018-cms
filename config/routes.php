<?php
/**
 * routes.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

use Cinemasunshine\PortalAdmin\Controller\AuthController;
use Cinemasunshine\PortalAdmin\Controller\AdvanceTicketController;
use Cinemasunshine\PortalAdmin\Controller\CampaignController;
use Cinemasunshine\PortalAdmin\Controller\IndexController;
use Cinemasunshine\PortalAdmin\Controller\NewsController;
use Cinemasunshine\PortalAdmin\Controller\TitleController;

use Cinemasunshine\PortalAdmin\Controller\API\CampaignController as CampaignApiController;
use Cinemasunshine\PortalAdmin\Controller\API\EditorController as EditorApiController;
use Cinemasunshine\PortalAdmin\Controller\API\NewsController as NewsApiController;
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
        $this->get('/publication', CampaignController::class . ':publication')->setName('campaign_publication');
        $this->post('/publication/update/{target}', CampaignController::class . ':publicationUpdate')->setName('campaign_publication_update');
        $this->get('/list', CampaignController::class . ':list')->setName('campaign_list');
        $this->get('/new', CampaignController::class . ':new')->setName('campaign_new');
        $this->post('/create', CampaignController::class . ':create')->setName('campaign_create');
        $this->get('/{id}/edit', CampaignController::class . ':edit')->setName('campaign_edit');
        $this->post('/{id}/update', CampaignController::class . ':update')->setName('campaign_update');
        $this->get('/{id}/delete', CampaignController::class . ':delete')->setName('campaign_delete');
    });
    
    $this->group('/news', function() {
        $this->get('/publication', NewsController::class . ':publication')->setName('news_publication');
        $this->post('/publication/update/{target}', NewsController::class . ':publicationUpdate')->setName('news_publication_update');
        $this->get('/list', NewsController::class . ':list')->setName('news_list');
        $this->get('/new', NewsController::class . ':new')->setName('news_new');
        $this->post('/create', NewsController::class . ':create')->setName('news_create');
        $this->get('/{id}/edit', NewsController::class . ':edit')->setName('news_edit');
        $this->post('/{id}/update', NewsController::class . ':update')->setName('news_update');
        $this->get('/{id}/delete', NewsController::class . ':delete')->setName('news_delete');
    });
    
    $this->group('/advance_ticket', function() {
        $this->get('/list', AdvanceTicketController::class . ':list')->setName('advance_ticket_list');
        $this->get('/new', AdvanceTicketController::class . ':new')->setName('advance_ticket_new');
        $this->post('/create', AdvanceTicketController::class . ':create')->setName('advance_ticket_create');
        $this->get('/{id}/edit', AdvanceTicketController::class . ':edit')->setName('advance_ticket_edit');
        $this->post('/{id}/update', AdvanceTicketController::class . ':update')->setName('advance_ticket_update');
    });
    
    $this->group('/api', function() {
        $this->group('/title', function() {
            $this->get('/list', TitleApiController::class . ':list');
        });
        
        $this->group('/campaign', function() {
            $this->get('/list', CampaignApiController::class . ':list');
        });
        
        $this->group('/news', function() {
            $this->get('/list', NewsApiController::class . ':list');
        });
        
        $this->group('/editor', function() {
            $this->post('/upload', EditorApiController::class . ':upload');
        });
    });
})->add(new AuthMiddleware($container));

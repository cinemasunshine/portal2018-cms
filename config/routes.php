<?php
/**
 * routes.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

use Cinemasunshine\PortalAdmin\Controller\IndexController;

$app->get('/', IndexController::class . ':index')->setName('homepage');

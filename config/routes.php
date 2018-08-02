<?php
/**
 * routes.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/', function (Request $request, Response $response) {
    return $this->view->render($response, 'hello.html.twig', [
        'name' => 'Slim',
    ]);
});

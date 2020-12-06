<?php

/**
 * BaseController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Base controller
 */
abstract class BaseController extends AbstractController
{
    /**
     * pre execute
     *
     * @param Request  $request
     * @param Response $response
     * @return void
     */
    protected function preExecute(Request $request, Response $response): void
    {
    }

    /**
     * post execute
     *
     * @param Request  $request
     * @param Response $response
     * @return void
     */
    protected function postExecute(Request $request, Response $response): void
    {
        $this->view->getEnvironment()->addGlobal('user', $this->auth->getUser());
        $this->view->getEnvironment()->addGlobal('alerts', $this->flash->getMessage('alerts'));
    }

    /**
     * @param Response $response
     * @param string   $template
     * @param array    $data
     * @return Response
     */
    protected function render(Response $response, string $template, array $data = []): Response
    {
        return $this->view->render($response, $template, $data);
    }
}

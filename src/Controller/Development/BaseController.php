<?php

/**
 * BaseController.php
 */

namespace App\Controller\Development;

use App\Controller\AbstractController;
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
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Base controller
 */
abstract class BaseController extends AbstractController
{
    protected function preExecute(Request $request, Response $response): void
    {
        $viewEnvironment = $this->view->getEnvironment();

        // おそらくrender()前に追加する必要があるので、今の仕組み上postExecute()では追加できない。
        $viewEnvironment->addGlobal('user', $this->auth->getUser());
        $viewEnvironment->addGlobal('alerts', $this->flash->getMessage('alerts'));
    }

    protected function postExecute(Request $request, Response $response): void
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function render(Response $response, string $template, array $data = []): Response
    {
        return $this->view->render($response, $template, $data);
    }
}

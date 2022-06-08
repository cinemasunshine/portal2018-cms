<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Handlers\NotFound as BaseHandler;
use Slim\Views\Twig;

/**
 * NotFound handler
 */
class NotFound extends BaseHandler
{
    protected Twig $view;

    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    /**
     * {@inheritdoc}
     */
    protected function renderHtmlNotFoundOutput(ServerRequestInterface $request)
    {
        if (APP_DEBUG) {
            return parent::renderHtmlNotFoundOutput($request);
        }

        return $this->view->fetch('error/client.html.twig', [
            'title' => 'Not Found',
            'status' => 404,
            'message' => 'このページは存在しません。',
        ]);
    }
}

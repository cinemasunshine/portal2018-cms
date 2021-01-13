<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response;

/**
 * Auth middleware class
 */
class AuthMiddleware extends AbstractMiddleware
{
    /**
     * Undocumented function
     *
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next)
    {
        $auth = $this->container->get('auth');

        if (! $auth->isAuthenticated()) {
            return $response->withRedirect(
                $this->container->get('router')->pathFor('login')
            );
        }

        $response = $next($request, $response);

        return $response;
    }
}

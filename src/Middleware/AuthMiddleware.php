<?php

/**
 * AuthMiddleware.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Middleware;

use Slim\Http\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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

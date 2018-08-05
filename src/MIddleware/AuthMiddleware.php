<?php
/**
 * AuthMiddleware.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Cinemasunshine\PortalAdmin\Auth;

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
        $auth = new Auth($this->container);
        
        if (!$auth->isAuthenticated()) {
            return $response->withRedirect(
                $this->container->get('router')->pathFor('login'));
        }
        
        $response = $next($request, $response);
        
        return $response;
    }
}
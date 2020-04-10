<?php
/**
 * NotAllowed.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Application\Handlers;

use Slim\Container;
use Slim\Handlers\NotAllowed as BaseHandler;

/**
 * NotAllowed handler
 */
class NotAllowed extends BaseHandler
{
    /** @var Container */
    protected $container;

    /** @var \Slim\Views\Twig */
    protected $view;

    /**
     * construct
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->view = $container->get('view');
    }

    /**
     * {@inheritdoc}
     */
    protected function renderHtmlNotAllowedMessage($methods)
    {
        if (APP_DEBUG) {
            return parent::renderHtmlNotAllowedMessage($methods);
        }

        return $this->view->fetch('error/client.html.twig', [
            'title' => 'Method Not Allowed',
            'status' => 405,
            'message' => 'このリクエストは許可されていません。',
        ]);
    }
}

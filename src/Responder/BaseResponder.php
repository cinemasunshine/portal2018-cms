<?php
/**
 * BaseResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Responder;

use Slim\Views\Twig;

/**
 * Base responder
 */
abstract class BaseResponder extends AbstractResponder
{
    /** @var Twig view */
    protected $view;
    
    /**
     * factory
     *
     * @param string $name
     * @param Twig   $view
     * @return AbstractResponder
     */
    final public static function factory(string $name, Twig $view) : AbstractResponder
    {
        $className =  __NAMESPACE__  . '\\' . $name . 'Responder';
        
        return new $className($view);
    }
    
    /**
     * contsruct
     *
     * @param Twig $view
     */
    public function __construct(Twig $view)
    {
        $this->view = $view;
    }
}

<?php

/**
 * ResponderFactory.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace App\Responder;

use Slim\Views\Twig;

/**
 * Responder factory class
 */
class ResponderFactory
{
    /**
     * factory
     *
     * @param string $name
     * @param Twig $view
     * @return BaseResponder
     */
    public static function factory(string $name, Twig $view): BaseResponder
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($name) . 'Responder';

        return new $class($view);
    }
}

<?php

/**
 * ResponderFactory.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace App\Responder\API;

/**
 * Responder factory class
 */
class ResponderFactory
{
    /**
     * factory
     *
     * @param string $name
     * @return BaseResponder
     */
    public static function factory(string $name): BaseResponder
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($name) . 'Responder';

        return new $class();
    }
}

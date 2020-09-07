<?php

/**
 * SessionManager.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Session;

use Laminas\Session\Config\ConfigInterface;
use Laminas\Session\Container;
use Laminas\Session\SessionManager as BaseManager;

/**
 * SessionManager class
 */
class SessionManager extends BaseManager
{
    /** @var Container[] */
    protected $containers = [];

    /**
     * construct
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        parent::__construct($config);

        Container::setDefaultManager($this);
    }

    /**
     * return session container
     *
     * @param string $name
     * @return Container
     */
    public function getContainer(string $name = 'default')
    {
        if (!isset($this->containers[$name])) {
            $this->containers[$name] = new Container($name);
        }

        return $this->containers[$name];
    }
}

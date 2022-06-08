<?php

declare(strict_types=1);

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
    protected array $containers = [];

    /**
     * construct
     */
    public function __construct(ConfigInterface $config)
    {
        parent::__construct($config);

        Container::setDefaultManager($this);
    }

    /**
     * return session container
     */
    public function getContainer(string $name = 'default'): Container
    {
        if (! isset($this->containers[$name])) {
            $this->containers[$name] = new Container($name);
        }

        return $this->containers[$name];
    }
}

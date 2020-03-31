<?php
/**
 * SessionManager.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Session;

use Laminas\Session\Config;
use Laminas\Session\Container;
use Laminas\Session\SessionManager as Base;

/**
 * SessionManager class
 */
class SessionManager extends Base
{
    /** @var Container[] */
    protected $containers = [];

    /**
     * construct
     *
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $config = new Config\SessionConfig();
        $config->setOptions($settings);

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

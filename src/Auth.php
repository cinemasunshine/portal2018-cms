<?php

namespace App;

use App\ORM\Entity\AdminUser;
use Doctrine\ORM\EntityManager;
use Laminas\Session\Container;
use Psr\Container\ContainerInterface;

class Auth
{
    /** @var EntityManager */
    protected $em;

    /** @var Container */
    protected $session;

    /** @var AdminUser|null */
    protected $user;

    public function __construct(ContainerInterface $container)
    {
        $this->em      = $container->get('em');
        $this->session = $container->get('sm')->getContainer('auth');
    }

    /**
     * login
     *
     * @param string $name
     * @param string $password
     * @return bool
     */
    public function login($name, $password)
    {
        $repository = $this->em->getRepository(AdminUser::class);

        /** @var AdminUser|null $adminUser */
        $adminUser = $repository->findOneByName($name);

        if (is_null($adminUser)) {
            return false;
        }

        if (! password_verify($password, $adminUser->getPassword())) {
            return false;
        }

        $this->user = $adminUser;

        $this->session['user_id'] = $adminUser->getId();

        return true;
    }

    /**
     * logout
     *
     * @todo Session Container自体をclear、またはremoveする
     *
     * @return void
     */
    public function logout()
    {
        $this->user = null;
        unset($this->session['user_id']);
    }

    /**
     * is authenticated
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return isset($this->session['user_id']);
    }

    /**
     * get user
     *
     * @return AdminUser|null
     */
    public function getUser()
    {
        if (! $this->isAuthenticated()) {
            return null;
        }

        if (! $this->user) {
            $repository = $this->em->getRepository(AdminUser::class);
            $this->user = $repository->findOneById($this->session['user_id']);
        }

        return $this->user;
    }
}

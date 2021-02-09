<?php

declare(strict_types=1);

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
     */
    public function login(string $name, string $password): bool
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
     */
    public function logout(): void
    {
        $this->user = null;
        unset($this->session['user_id']);
    }

    /**
     * is authenticated
     */
    public function isAuthenticated(): bool
    {
        return isset($this->session['user_id']);
    }

    /**
     * get user
     */
    public function getUser(): ?AdminUser
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

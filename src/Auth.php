<?php
/**
 * Auth.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin;

use Psr\Container\ContainerInterface;

use Cinemasunshine\PortalAdmin\ORM\Entity\AdminUser;

class Auth
{
    /** @var \Doctrine\ORM\EntityManager */
    protected $em;
    
    /** @var AdminUser */
    protected $user;
    
    public function __construct(ContainerInterface $container)
    {
        $this->em = $container->get('em');
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
        $adminUser = $repository->findOneByName($name);
        
        if (is_null($adminUser)) {
            return false;
        }
        
        /** @var AdminUser $adminUser */
        
        if (!password_verify($password, $adminUser->getPassword())) {
            return false;
        }
        
        $this->user = $adminUser;
        $_SESSION['auth.user_id'] = $adminUser->getId();
        
        return true;
    }
    
    /**
     * logout
     *
     * @return void
     */
    public function logout()
    {
        $this->user = null;
        unset($_SESSION['auth.user_id']);
    }
    
    /**
     * is authenticated
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return isset($_SESSION['auth.user_id']);
    }
    
    /**
     * get user
     *
     * @return AdminUser|null
     */
    public function getUser()
    {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        if (!$this->user) {
            $repository = $this->em->getRepository(AdminUser::class);
            $this->user = $repository->findOneById($_SESSION['auth.user_id']);
        }
        
        return $this->user;
    }
}
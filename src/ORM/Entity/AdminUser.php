<?php

/**
 * AdminUser.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\AdminUser as BaseAdminUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * AdminUser entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\AdminUserRepository")
 * @ORM\Table(name="admin_user", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class AdminUser extends BaseAdminUser
{
    /** @var array */
    protected static $groups = [
        self::GROUP_MASTER  => 'マスター',
        self::GROUP_MANAGER => 'マネージャー',
        self::GROUP_THEATER => '劇場',
    ];

    /**
     * return groups
     *
     * @return array
     */
    public static function getGroups()
    {
        return self::$groups;
    }

    /**
     * is group
     *
     * @param int $group
     * @return boolean
     */
    public function isGroup(int $group)
    {
        return $this->getGroup() === $group;
    }

    /**
     * is master group
     *
     * @return boolean
     */
    public function isMaster()
    {
        return $this->isGroup(self::GROUP_MASTER);
    }

    /**
     * is manager group
     *
     * @return boolean
     */
    public function isManager()
    {
        return $this->isGroup(self::GROUP_MANAGER);
    }

    /**
     * is theater group
     *
     * @return boolean
     */
    public function isTheater()
    {
        return $this->isGroup(self::GROUP_THEATER);
    }

    /**
     * @return string
     */
    public function getGroupLabel(): string
    {
        return self::$groups[$this->getGroup()] ?? '';
    }
}

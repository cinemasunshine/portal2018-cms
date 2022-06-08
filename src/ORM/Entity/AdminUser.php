<?php

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
    /** @var array<int, string> */
    protected static array $groups = [
        self::GROUP_MASTER  => 'マスター',
        self::GROUP_MANAGER => 'マネージャー',
        self::GROUP_THEATER => '劇場',
    ];

    /**
     * @return array<int, string>
     */
    public static function getGroups(): array
    {
        return self::$groups;
    }

    public function isGroup(int $group): bool
    {
        return $this->getGroup() === $group;
    }

    /**
     * is master group
     */
    public function isMaster(): bool
    {
        return $this->isGroup(self::GROUP_MASTER);
    }

    /**
     * is manager group
     */
    public function isManager(): bool
    {
        return $this->isGroup(self::GROUP_MANAGER);
    }

    /**
     * is theater group
     */
    public function isTheater(): bool
    {
        return $this->isGroup(self::GROUP_THEATER);
    }

    public function getGroupLabel(): string
    {
        return self::$groups[$this->getGroup()] ?? '';
    }
}

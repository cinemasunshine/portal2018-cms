<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * NewsPublication interface
 */
interface NewsPublicationInterface
{
    /**
     * get newsList
     */
    public function getNewsList(): Collection;
}

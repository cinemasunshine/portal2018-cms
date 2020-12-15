<?php

declare(strict_types=1);

namespace App\ORM\Entity;

use Cinemasunshine\ORM\Entities\AdvanceSale as BaseAdvanceSale;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * AdvanceSale entity class
 *
 * @ORM\Entity(repositoryClass="App\ORM\Repository\AdvanceSaleRepository")
 * @ORM\Table(name="advance_sale", options={"collate"="utf8mb4_general_ci"})
 * @ORM\HasLifecycleCallbacks
 */
class AdvanceSale extends BaseAdvanceSale
{
    /**
     * get active advance_tickets
     *
     * @return Collection
     */
    public function getActiveAdvanceTickets()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('isDeleted', false));

        /**
         * matching()を使うとindexByオプションの設定が消えてしまう
         * https://github.com/doctrine/doctrine2/issues/4693
         */
        $tmpResults = $this->getAdvanceTickets()->matching($criteria);

        // idをindexにしたcollectionを作り直す
        $results = new ArrayCollection();

        foreach ($tmpResults as $tmp) {
            /** @var AdvanceTicket $tmp */
            $results->set($tmp->getId(), $tmp);
        }

        return $results;
    }
}

<?php

/**
 * DoctrinePaginator.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Pagination;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as WrapPaginator;

/**
 * Doctrine paginator class
 */
class DoctrinePaginator extends AbstractPaginator
{
    /** @var WrapPaginator */
    protected $wrapPaginator;

    /**
     * construct
     *
     * @param Query   $query               A Doctrine ORM query or query builder.
     * @param int     $page
     * @param int     $maxPerPage
     * @param boolean $fetchJoinCollection Whether the query joins a collection (true by default).
     */
    public function __construct(Query $query, int $page, int $maxPerPage, $fetchJoinCollection = true)
    {
        $this->initalize($query, $page, $maxPerPage, $fetchJoinCollection);
    }

    /**
     * initalize
     *
     * @param Query   $query               A Doctrine ORM query or query builder.
     * @param int     $page
     * @param int     $maxPerPage
     * @param boolean $fetchJoinCollection Whether the query joins a collection (true by default).
     */
    protected function initalize(Query $query, int $page, int $maxPerPage, $fetchJoinCollection = true)
    {
        $this->page       = $page;
        $this->maxPerPage = $maxPerPage;

        $offset = ($page - 1) * $maxPerPage;
        $query
            ->setFirstResult($offset)
            ->setMaxResults($maxPerPage);

        $paginator = new WrapPaginator($query, $fetchJoinCollection);

        $this->wrapPaginator = $paginator;
        $this->numResults    = count($paginator);

        if (0 === $page || 0 === $maxPerPage || 0 === $this->numResults) {
            $this->lastPage = 0;
        } else {
            $this->lastPage = (int) ceil($this->numResults / $maxPerPage);
        }

        $this->resultsInPage = [];

        foreach ($paginator as $row) {
            $this->resultsInPage[] = $row;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResultsInPage()
    {
        return $this->resultsInPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getNumResults(): int
    {
        return $this->numResults;
    }
}

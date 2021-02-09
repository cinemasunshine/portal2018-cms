<?php

declare(strict_types=1);

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
     * @param Query $query               A Doctrine ORM query or query builder.
     * @param bool  $fetchJoinCollection Whether the query joins a collection (true by default).
     */
    public function __construct(Query $query, int $page, int $maxPerPage, bool $fetchJoinCollection = true)
    {
        $this->initalize($query, $page, $maxPerPage, $fetchJoinCollection);
    }

    /**
     * @param Query $query               A Doctrine ORM query or query builder.
     * @param bool  $fetchJoinCollection Whether the query joins a collection (true by default).
     */
    protected function initalize(Query $query, int $page, int $maxPerPage, bool $fetchJoinCollection = true): void
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

        if ($page === 0 || $maxPerPage === 0 || $this->numResults === 0) {
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
    public function getResultsInPage(): ?array
    {
        return $this->resultsInPage;
    }

    public function getNumResults(): int
    {
        return $this->numResults;
    }
}

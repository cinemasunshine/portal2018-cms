<?php

declare(strict_types=1);

namespace App\Pagination;

/**
 * Abstract Paginator class
 */
abstract class AbstractPaginator
{
    protected int $page = 1;

    protected int $maxPerPage = 0;

    protected int $lastPage = 1;

    protected int $numResults = 0;

    protected int $currentMaxLink = 1;

    /** @var array<mixed>|null */
    protected ?array $resultsInPage = null;

    /**
     * Returns the number of itenms in current page
     *
     * @return array<mixed>|null
     */
    abstract public function getResultsInPage(): ?array;

    /**
     * Return the total number of items
     */
    abstract public function getNumResults(): int;

    /**
     * Returns an array of page numbers to use in pagination links.
     *
     * @param  int $numLinks The maximum number of page numbers to return
     * @return int[]
     */
    public function getLinks(int $numLinks = 5): array
    {
        $links = [];
        $tmp   = $this->page - floor($numLinks / 2);
        $check = $this->lastPage - $numLinks + 1;
        $limit = $check > 0 ? $check : 1;
        $begin = $tmp > 0 ? ($tmp > $limit ? $limit : $tmp) : 1;

        $i = (int) $begin;

        while ($i < $begin + $numLinks && $i <= $this->lastPage) {
            $links[] = $i++;
        }

        $this->currentMaxLink = count($links) ? $links[count($links) - 1] : 1;

        return $links;
    }

    /**
     * Returns the current page.
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Returns the first page number.
     */
    public function getFirstPage(): int
    {
        return 1;
    }

    /**
     * Returns the last page number.
     */
    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    /**
     * Returns the next page.
     */
    public function getNextPage(): int
    {
        return min($this->getPage() + 1, $this->getLastPage());
    }

    /**
     * Returns the previous page.
     */
    public function getPreviousPage(): int
    {
        return max($this->getPage() - 1, $this->getFirstPage());
    }

    /**
     * Returns the maximum number of results per page.
     */
    public function getMaxPerPage(): int
    {
        return $this->maxPerPage;
    }

    /**
     * Returns true if on the first page.
     */
    public function isFirstPage(): bool
    {
        return $this->getPage() === 1;
    }

    /**
     * Returns true if on the last page.
     */
    public function isLastPage(): bool
    {
        return $this->getPage() === $this->getLastPage();
    }

    /**
     * Returns the first index on the current page.
     */
    public function getFirstIndice(): int
    {
        if ($this->getPage() === 0) {
            return 1;
        }

        return ($this->getPage() - 1) * $this->getMaxPerPage() + 1;
    }

    /**
     * Returns the last index on the current page.
     */
    public function getLastIndice(): int
    {
        if ($this->getPage() === 0) {
            return $this->getNumResults();
        }

        if ($this->getPage() * $this->getMaxPerPage() >= $this->getNumResults()) {
            return $this->getNumResults();
        }

        return $this->getPage() * $this->getMaxPerPage();
    }
}

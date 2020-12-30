<?php

namespace App\Pagination;

/**
 * Abstract Paginator class
 */
abstract class AbstractPaginator
{
    /** @var int */
    protected $page = 1;

    /** @var int */
    protected $maxPerPage = 0;

    /** @var int */
    protected $lastPage = 1;

    /** @var int */
    protected $numResults = 0;

    /** @var int */
    protected $currentMaxLink = 1;

    /** @var null|array */
    protected $resultsInPage = null;

    /**
     * Returns the number of itenms in current page
     *
     * @return array
     */
    abstract public function getResultsInPage();

    /**
     * Return the total number of items
     *
     * @return int
     */
    abstract public function getNumResults(): int;

    /**
     * Returns an array of page numbers to use in pagination links.
     *
     * @param  int $numLinks The maximum number of page numbers to return
     * @return array
     */
    public function getLinks(int $numLinks = 5)
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
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Returns the first page number.
     *
     * @return int
     */
    public function getFirstPage()
    {
        return 1;
    }

    /**
     * Returns the last page number.
     *
     * @return int
     */
    public function getLastPage()
    {
        return $this->lastPage;
    }

    /**
     * Returns the next page.
     *
     * @return int
     */
    public function getNextPage()
    {
        return min($this->getPage() + 1, $this->getLastPage());
    }

    /**
     * Returns the previous page.
     *
     * @return int
     */
    public function getPreviousPage()
    {
        return max($this->getPage() - 1, $this->getFirstPage());
    }

    /**
     * Returns the maximum number of results per page.
     *
     * @return integer
     */
    public function getMaxPerPage()
    {
        return $this->maxPerPage;
    }

    /**
     * Returns true if on the first page.
     *
     * @return bool
     */
    public function isFirstPage()
    {
        return $this->getPage() == 1;
    }

    /**
     * Returns true if on the last page.
     *
     * @return bool
     */
    public function isLastPage()
    {
        return $this->getPage() == $this->getLastPage();
    }

    /**
     * Returns the first index on the current page.
     *
     * @return int
     */
    public function getFirstIndice()
    {
        if ($this->getPage() == 0) {
            return 1;
        }

        return ($this->getPage() - 1) * $this->getMaxPerPage() + 1;
    }

    /**
     * Returns the last index on the current page.
     *
     * @return int
     */
    public function getLastIndice()
    {
        if ($this->getPage() == 0) {
            return $this->getNumResults();
        }

        if ($this->getPage() * $this->getMaxPerPage() >= $this->getNumResults()) {
            return $this->getNumResults();
        }

        return $this->getPage() * $this->getMaxPerPage();
    }
}

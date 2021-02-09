<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Psr\Http\Message\UriInterface;

/**
 * redirect exception
 */
class RedirectException extends Exception
{
    /** @var string|UriInterface */
    protected $url;

    /** @var int|null */
    protected $status;

    /**
     * @param string|UriInterface $url    The redirect destination.
     * @param int|null            $status The redirect HTTP status code.
     */
    public function __construct($url, ?int $status = null)
    {
        $this->url    = $url;
        $this->status = $status;

        parent::__construct('redirect');
    }

    /**
     * @return string|UriInterface
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }
}

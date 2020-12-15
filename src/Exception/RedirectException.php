<?php

namespace App\Exception;

use Psr\Http\Message\UriInterface;

/**
 * redirect exception
 */
class RedirectException extends \Exception
{
    protected $url;
    protected $status;

    /**
     * Undocumented function
     *
     * @param string|UriInterface $url    The redirect destination.
     * @param int|null            $status The redirect HTTP status code.
     */
    public function __construct($url, $status = null)
    {
        $this->url    = $url;
        $this->status = $status;

        parent::__construct('redirect');
    }

    /**
     * get url
     *
     * @return string|UriInterface
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * get status
     *
     * @return int|null
     */
    public function getStatus()
    {
        return $this->status;
    }
}

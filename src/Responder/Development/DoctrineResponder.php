<?php

/**
 * DoctrineResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Responder\Development;

use Slim\Collection;
use Slim\Http\Response;

/**
 * Doctrine responder
 */
class DoctrineResponder extends BaseResponder
{
    /**
     * cache stats
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function cacheStats(Response $response, Collection $data)
    {
        $dump = var_export($data->all(), true);
        return $response->write('<pre>' . $dump . '</pre>');
    }

    /**
     * cache clear
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function cacheClear(Response $response, Collection $data)
    {
        $dump = var_export($data->all(), true);
        return $response->write('<pre>' . $dump . '</pre>');
    }
}

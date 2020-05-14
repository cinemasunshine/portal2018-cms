<?php

/**
 * CacheResponder.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Responder\Development;

use Slim\Collection;
use Slim\Http\Response;

/**
 * Cache responder
 */
class CacheResponder extends BaseResponder
{
    /**
     * clear query
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function stats(Response $response, Collection $data)
    {
        $dump = var_export($data->all(), true);
        return $response->write('<pre>' . $dump . '</pre>');
    }

    /**
     * clear query
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function clearQuery(Response $response, Collection $data)
    {
        $dump = var_export($data->all(), true);
        return $response->write('<pre>' . $dump . '</pre>');
    }

    /**
     * clear metadata
     *
     * @param Response   $response
     * @param Collection $data
     * @return Response
     */
    public function clearMetadata(Response $response, Collection $data)
    {
        $dump = var_export($data->all(), true);
        return $response->write('<pre>' . $dump . '</pre>');
    }
}

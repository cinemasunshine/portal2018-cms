<?php

/**
 * CacheController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace Cinemasunshine\PortalAdmin\Controller\Development;

/**
 * Cache controller
 *
 * Webからキャッシュ操作する機能を提供する。
 * WinCacheのキャッシュはWebとCLIが別になっていて、コンソールからはクリアできないらしい。
 * よってその代替として実装。
 */
class CacheController extends BaseController
{
    /**
     * Gets the cache driver implementation that is used for the query cache (SQL cache).
     *
     * @return \Doctrine\Common\Cache\Cache|null
     */
    protected function getQueryCacheImpl()
    {
        return $this->em->getConfiguration()->getQueryCacheImpl();
    }

    /**
     * Gets the cache driver implementation that is used for metadata caching.
     *
     * @return \Doctrine\Common\Cache\Cache|null
     */
    protected function getMetadataCacheImpl()
    {
        return $this->em->getConfiguration()->getMetadataCacheImpl();
    }

    /**
     * stats
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeStats($request, $response, $args)
    {
        $queryCacheDriver = $this->getQueryCacheImpl();
        $this->data->set('query', $queryCacheDriver->getStats());

        $metadataCacheDriver = $this->getMetadataCacheImpl();
        $this->data->set('metadata', $metadataCacheDriver->getStats());
    }

    /**
     * clear query action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     * @see Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand::execute()
     */
    public function executeClearQuery($request, $response, $args)
    {
        $cacheDriver = $this->getQueryCacheImpl();
        $flush = ($request->getParam('flush') === 'true');

        $message = $this->doClear($cacheDriver, $flush);

        $this->data->set('message', $message);
    }

    /**
     * clear metadata action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     * @see Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand::execute()
     */
    public function executeClearMetadata($request, $response, $args)
    {
        $cacheDriver = $this->getMetadataCacheImpl();
        $flush = ($request->getParam('flush') === 'true');

        $message = $this->doClear($cacheDriver, $flush);

        $this->data->set('message', $message);
    }

    /**
     * do clear
     *
     * @param \Doctrine\Common\Cache\CacheProvider|null $cacheDriver
     * @param boolean $flush
     * @return string
     */
    protected function doClear($cacheDriver, bool $flush = false): string
    {
        if (!$cacheDriver) {
            throw new \InvalidArgumentException('No Query cache driver is configured on given EntityManager.');
        }

        $result  = $cacheDriver->deleteAll();
        $message = ($result) ? 'Successfully deleted cache entries.' : 'No cache entries were deleted.';

        if ($flush) {
            $result  = $cacheDriver->flushAll();
            $message = ($result) ? 'Successfully flushed cache entries.' : $message;
        }

        if (!$result) {
            throw new \RuntimeException($message);
        }

        return $message;
    }
}

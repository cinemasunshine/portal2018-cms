<?php

/**
 * DoctrineController.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

namespace App\Controller\Development;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;

/**
 * Doctrine controller
 *
 * Webからキャッシュ操作する機能を提供する。
 * WinCacheのキャッシュはWebとCLIが別になっていて、コンソールからはクリアできないらしい。
 * よってその代替として実装。
 */
class DoctrineController extends BaseController
{
    /**
     * Gets the cache driver implementation that is used for the query cache (SQL cache).
     *
     * @return Cache|null
     */
    protected function getQueryCacheImpl()
    {
        return $this->em->getConfiguration()->getQueryCacheImpl();
    }

    /**
     * Gets the cache driver implementation that is used for metadata caching.
     *
     * @return Cache|null
     */
    protected function getMetadataCacheImpl()
    {
        return $this->em->getConfiguration()->getMetadataCacheImpl();
    }

    /**
     * cache stats
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     */
    public function executeCacheStats($request, $response, $args)
    {
        $queryCacheDriver = $this->getQueryCacheImpl();
        $this->data->set('query', $queryCacheDriver->getStats());

        $metadataCacheDriver = $this->getMetadataCacheImpl();
        $this->data->set('metadata', $metadataCacheDriver->getStats());
    }

    /**
     * cache clear action
     *
     * @param \Slim\Http\Request  $request
     * @param \Slim\Http\Response $response
     * @param array               $args
     * @return string|void
     * @see Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand::execute()
     */
    public function executeCacheClear($request, $response, $args)
    {
        $target = $args['target'];

        if ($target === 'query') {
            $cacheDriver = $this->getQueryCacheImpl();
        } elseif ($target === 'metadata') {
            $cacheDriver = $this->getMetadataCacheImpl();
        } else {
            throw new \InvalidArgumentException('Invalid "target".');
        }

        if (! $cacheDriver) {
            throw new \InvalidArgumentException('No cache driver is configured on given EntityManager.');
        }

        if (! $cacheDriver instanceof CacheProvider) {
            throw new \InvalidArgumentException('This cache driver does not support clear.');
        }

        $flush = ($request->getParam('flush') === 'true');

        $message = $this->doClear($cacheDriver, $flush);

        $this->data->set('message', $message);
    }

    /**
     * @param CacheProvider $cacheDriver
     * @param boolean       $flush
     * @return string
     */
    protected function doClear(CacheProvider $cacheDriver, bool $flush = false): string
    {
        $result  = $cacheDriver->deleteAll();
        $message = ($result) ? 'Successfully deleted cache entries.' : 'No cache entries were deleted.';

        if ($flush) {
            $result  = $cacheDriver->flushAll();
            $message = ($result) ? 'Successfully flushed cache entries.' : $message;
        }

        if (! $result) {
            throw new \RuntimeException($message);
        }

        return $message;
    }
}

<?php

namespace App\Controller\Development;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;
use InvalidArgumentException;
use RuntimeException;
use Slim\Http\Request;
use Slim\Http\Response;

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
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeCacheStats(Request $request, Response $response, array $args)
    {
        $queryCacheDriver = $this->getQueryCacheImpl();
        $query            = $queryCacheDriver->getStats();

        $metadataCacheDriver = $this->getMetadataCacheImpl();
        $metadata            = $metadataCacheDriver->getStats();

        $data = [
            'query' => $query,
            'metadata' => $metadata,
        ];

        return $response->write('<pre>' . var_export($data, true) . '</pre>');
    }

    /**
     * cache clear action
     *
     * @see Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand::execute()
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     * @return Response
     */
    public function executeCacheClear(Request $request, Response $response, array $args)
    {
        $target = $args['target'];

        if ($target === 'query') {
            $cacheDriver = $this->getQueryCacheImpl();
        } elseif ($target === 'metadata') {
            $cacheDriver = $this->getMetadataCacheImpl();
        } else {
            throw new InvalidArgumentException('Invalid "target".');
        }

        if (! $cacheDriver) {
            throw new InvalidArgumentException('No cache driver is configured on given EntityManager.');
        }

        if (! $cacheDriver instanceof CacheProvider) {
            throw new InvalidArgumentException('This cache driver does not support clear.');
        }

        $flush = ($request->getParam('flush') === 'true');

        $message = $this->doClear($cacheDriver, $flush);

        $data = ['message' => $message];

        return $response->write('<pre>' . var_export($data, true) . '</pre>');
    }

    protected function doClear(CacheProvider $cacheDriver, bool $flush = false): string
    {
        $result  = $cacheDriver->deleteAll();
        $message = ($result) ? 'Successfully deleted cache entries.' : 'No cache entries were deleted.';

        if ($flush) {
            $result  = $cacheDriver->flushAll();
            $message = ($result) ? 'Successfully flushed cache entries.' : $message;
        }

        if (! $result) {
            throw new RuntimeException($message);
        }

        return $message;
    }
}

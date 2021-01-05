<?php

/**
 * container.php
 *
 * AbstractControllerのphpdoc更新を推奨。
 *
 * @see App\Controller\AbstractController\__call()
 */

// phpcs:disable SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration
/** @var \Slim\App $app */
// phpcs:enable

$container = $app->getContainer();

/**
 * view
 *
 * @link https://www.slimframework.com/docs/v3/features/templates.html
 *
 * @return \Slim\Views\Twig
 */
$container['view'] = static function ($container) {
    $settings = $container->get('settings')['view'];

    $view = new \Slim\Views\Twig($settings['template_path'], $settings['settings']);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri    = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    // add Extension
    $view->addExtension(new \Twig\Extension\DebugExtension());

    $view->addExtension(new \App\Twig\Extension\AzureStorageExtension(
        $container->get('bc'),
        $container->get('settings')['storage']['public_endpoint']
    ));

    return $view;
};

/**
 * logger
 *
 * @link https://github.com/Seldaek/monolog
 *
 * @return \Monolog\Logger
 */
$container['logger'] = static function ($container) {
    $settings = $container->get('settings')['logger'];

    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\PsrLogMessageProcessor());
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushProcessor(new Monolog\Processor\IntrospectionProcessor());
    $logger->pushProcessor(new Monolog\Processor\WebProcessor());
    $logger->pushProcessor(new Monolog\Processor\MemoryUsageProcessor());
    $logger->pushProcessor(new Monolog\Processor\MemoryPeakUsageProcessor());

    if (isset($settings['browser_console'])) {
        $browserConsoleSettings = $settings['browser_console'];

        $logger->pushHandler(new \Monolog\Handler\BrowserConsoleHandler(
            $browserConsoleSettings['level']
        ));
    }

    $azureBlobStorageSettings = $settings['azure_blob_storage'];
    $azureBlobStorageHandler  = new App\Logger\Handler\AzureBlobStorageHandler(
        $container->get('bc'),
        $azureBlobStorageSettings['container'],
        $azureBlobStorageSettings['blob'],
        $azureBlobStorageSettings['level']
    );

    $bufferSettings = $settings['buffer'];
    $logger->pushHandler(new Monolog\Handler\BufferHandler(
        $azureBlobStorageHandler,
        $bufferSettings['limit']
    ));

    return $logger;
};

/**
 * Doctrine entity manager
 *
 * @return \Doctrine\ORM\EntityManager
 */
$container['em'] = static function ($container) {
    $settings = $container->get('settings')['doctrine'];
    $proxyDir = APP_ROOT . '/src/ORM/Proxy';

    if ($settings['cache'] === 'wincache') {
        $cache = new \Doctrine\Common\Cache\WinCacheCache();
    } else {
        $cache = new \Doctrine\Common\Cache\ArrayCache();
    }

    /**
     * 第５引数について、他のアノテーションとの競合を避けるためSimpleAnnotationReaderは使用しない。
     *
     * @Entity => @ORM\Entity などとしておく。
     */
    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
        $settings['metadata_dirs'],
        $settings['dev_mode'],
        $proxyDir,
        $cache,
        false
    );

    $config->setProxyNamespace('App\ORM\Proxy');

    $logger = new \App\Logger\DbalLogger($container->get('logger'));
    $config->setSQLLogger($logger);

    return \Doctrine\ORM\EntityManager::create($settings['connection'], $config);
};

/**
 * session manager
 *
 * @return \App\Session\SessionManager
 */
$container['sm'] = static function ($container) {
    $settings = $container->get('settings')['session'];

    $config = new Laminas\Session\Config\SessionConfig();
    $config->setOptions($settings);

    return new \App\Session\SessionManager($config);
};

/**
 * Flash Messages
 *
 * @return \Slim\Flash\Messages
 */
$container['flash'] = static function ($container) {
    $session = $container->get('sm')->getContainer('flash');

    return new \Slim\Flash\Messages($session);
};

/**
 * auth
 *
 * @return \App\Auth
 */
$container['auth'] = static function ($container) {
    return new App\Auth($container);
};

/**
 * Azure Blob Storage Client
 *
 * @link https://github.com/Azure/azure-storage-php/tree/master/azure-storage-blob
 *
 * @return \MicrosoftAzure\Storage\Blob\BlobRestProxy
 */
$container['bc'] = static function ($container) {
    $settings = $container->get('settings')['storage'];
    $protocol = $settings['secure'] ? 'https' : 'http';

    $connection = sprintf(
        'DefaultEndpointsProtocol=%s;AccountName=%s;AccountKey=%s;',
        $protocol,
        $settings['account_name'],
        $settings['account_key']
    );

    if ($settings['blob_endpoint']) {
        $connection .= sprintf('BlobEndpoint=%s;', $settings['blob_endpoint']);
    }

    return \MicrosoftAzure\Storage\Blob\BlobRestProxy::createBlobService($connection);
};

$container['errorHandler'] = static function ($container) {
    return new \App\Application\Handlers\Error(
        $container->get('logger'),
        $container->get('settings')['displayErrorDetails']
    );
};

$container['phpErrorHandler'] = static function ($container) {
    return new \App\Application\Handlers\PhpError(
        $container->get('logger'),
        $container->get('settings')['displayErrorDetails']
    );
};

$container['notFoundHandler'] = static function ($container) {
    return new \App\Application\Handlers\NotFound(
        $container->get('view')
    );
};

$container['notAllowedHandler'] = static function ($container) {
    return new \App\Application\Handlers\NotAllowed(
        $container->get('view')
    );
};

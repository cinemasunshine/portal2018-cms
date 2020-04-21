<?php

/**
 * container.php
 *
 * AbstractControllerのphpdoc更新を推奨。
 *
 * @see Cinemasunshine\PortalAdmin\Controller\AbstractController\__call()
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

/** @var \Slim\App $app */
$container = $app->getContainer();

/**
 * view
 *
 * @link https://www.slimframework.com/docs/v3/features/templates.html
 *
 * @return \Slim\Views\Twig
 */
$container['view'] = function ($container) {
    $settings = $container->get('settings')['view'];

    $view = new \Slim\Views\Twig($settings['template_path'], $settings['settings']);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    // add Extension
    $view->addExtension(new \Twig\Extension\DebugExtension());

    $view->addExtension(new \Cinemasunshine\PortalAdmin\Twig\Extension\AzureStorageExtension(
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
$container['logger'] = function ($container) {
    $settings = $container->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);

    $logger->pushProcessor(new Monolog\Processor\PsrLogMessageProcessor());
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushProcessor(new Monolog\Processor\IntrospectionProcessor());
    $logger->pushProcessor(new Monolog\Processor\WebProcessor());
    $logger->pushProcessor(new Monolog\Processor\MemoryUsageProcessor());
    $logger->pushProcessor(new Monolog\Processor\MemoryPeakUsageProcessor());

    if (isset($settings['chrome_php'])) {
        $chromePhpSettings = $settings['chrome_php'];
        $logger->pushHandler(new Monolog\Handler\ChromePHPHandler(
            $chromePhpSettings['level']
        ));
    }

    $azureBlobStorageSettings = $settings['azure_blob_storage'];
    $azureBlobStorageHandler = new Cinemasunshine\PortalAdmin\Logger\Handler\AzureBlobStorageHandler(
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
$container['em'] = function ($container) {
    $settings = $container->get('settings')['doctrine'];
    $proxyDir = APP_ROOT . '/src/ORM/Proxy';

    /**
     * 第５引数について、他のアノテーションとの競合を避けるためSimpleAnnotationReaderは使用しない。
     * @Entity => @ORM\Entity などとしておく。
     */
    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
        $settings['metadata_dirs'],
        $settings['dev_mode'],
        $proxyDir,
        null,
        false
    );

    $config->setProxyNamespace('Cinemasunshine\PortalAdmin\ORM\Proxy');

    $logger = new \Cinemasunshine\PortalAdmin\Logger\DbalLogger($container->get('logger'));
    $config->setSQLLogger($logger);

    return \Doctrine\ORM\EntityManager::create($settings['connection'], $config);
};

/**
 * session manager
 *
 * @return \Cinemasunshine\PortalAdmin\Session\SessionManager
 */
$container['sm'] = function ($container) {
    $settings = $container->get('settings')['session'];

    return new \Cinemasunshine\PortalAdmin\Session\SessionManager($settings);
};

/**
 * Flash Messages
 *
 * @return \Slim\Flash\Messages
 */
$container['flash'] = function ($container) {
    $session = $container->get('sm')->getContainer('flash');

    return new \Slim\Flash\Messages($session);
};

/**
 * auth
 *
 * @return \Cinemasunshine\PortalAdmin\Auth
 */
$container['auth'] = function ($container) {
    return new Cinemasunshine\PortalAdmin\Auth($container);
};

/**
 * Azure Blob Storage Client
 *
 * @link https://github.com/Azure/azure-storage-php/tree/master/azure-storage-blob
 * @return \MicrosoftAzure\Storage\Blob\BlobRestProxy
 */
$container['bc'] = function ($container) {
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

$container['errorHandler'] = function ($container) {
    return new \Cinemasunshine\PortalAdmin\Application\Handlers\Error($container);
};

$container['phpErrorHandler'] = function ($container) {
    return new \Cinemasunshine\PortalAdmin\Application\Handlers\PhpError($container);
};

$container['notFoundHandler'] = function ($container) {
    return new \Cinemasunshine\PortalAdmin\Application\Handlers\NotFound($container);
};

$container['notAllowedHandler'] = function ($container) {
    return new \Cinemasunshine\PortalAdmin\Application\Handlers\NotAllowed($container);
};

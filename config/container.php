<?php

declare(strict_types=1);

/**
 * AbstractControllerのphpdoc更新を推奨。
 *
 * @see App\Controller\AbstractController\__call()
 */

use App\Application\Handlers\Error;
use App\Application\Handlers\NotAllowed;
use App\Application\Handlers\NotFound;
use App\Application\Handlers\PhpError;
use App\Auth;
use App\Logger\DbalLogger;
use App\Session\SessionManager;
use App\Twig\Extension\AzureStorageExtension;
use Blue32a\MonologGoogleCloudLoggingHandler\GoogleCloudLoggingHandler;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\BufferHandler;
use Monolog\Logger;
use Slim\App as SlimApp;
use Slim\Flash\Messages as FlashMessages;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Twig\Extension\DebugExtension;

// phpcs:disable SlevomatCodingStandard.Commenting.InlineDocCommentDeclaration
/** @var SlimApp $app */
// phpcs:enable

$container = $app->getContainer();

/**
 * view
 *
 * @link https://www.slimframework.com/docs/v3/features/templates.html
 *
 * @return Twig
 */
$container['view'] = static function ($container) {
    $settings = $container->get('settings')['view'];

    $view = new Twig($settings['template_path'], $settings['settings']);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri    = Uri::createFromEnvironment(new Environment($_SERVER));
    $view->addExtension(new TwigExtension($router, $uri));

    // add Extension
    $view->addExtension(new DebugExtension());

    $view->addExtension(new AzureStorageExtension(
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
 * @return Logger
 */
$container['logger'] = static function ($container) {
    $settings = $container->get('settings')['logger'];

    $logger = new Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\PsrLogMessageProcessor());
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushProcessor(new Monolog\Processor\IntrospectionProcessor());
    $logger->pushProcessor(new Monolog\Processor\WebProcessor());
    $logger->pushProcessor(new Monolog\Processor\MemoryUsageProcessor());
    $logger->pushProcessor(new Monolog\Processor\MemoryPeakUsageProcessor());

    if (isset($settings['browser_console'])) {
        $browserConsoleSettings = $settings['browser_console'];

        $logger->pushHandler(new BrowserConsoleHandler(
            $browserConsoleSettings['level']
        ));
    }

    if (isset($settings['google_cloud_logging'])) {
        $googleCloudLoggingSettings = $settings['google_cloud_logging'];
        $googleCloudLoggingClient   = GoogleCloudLoggingHandler::factoryLoggingClient(
            $googleCloudLoggingSettings['client_options']
        );
        $googleCloudLoggingHandler  = new GoogleCloudLoggingHandler(
            $googleCloudLoggingSettings['name'],
            $googleCloudLoggingClient,
            [],
            $googleCloudLoggingSettings['level']
        );

        $bufferSettings = $settings['buffer'];
        $logger->pushHandler(new BufferHandler(
            $googleCloudLoggingHandler,
            $bufferSettings['limit']
        ));
    }

    return $logger;
};

/**
 * Doctrine entity manager
 *
 * @return EntityManager
 */
$container['em'] = static function ($container) {
    $settings = $container->get('settings')['doctrine'];
    $proxyDir = APP_ROOT . '/src/ORM/Proxy';

    if ($settings['cache'] === 'filesystem') {
        $cache = new FilesystemCache($settings['filesystem_cache_dir']);
    } else {
        $cache = new ArrayCache();
    }

    /**
     * 第５引数について、他のアノテーションとの競合を避けるためSimpleAnnotationReaderは使用しない。
     *
     * @Entity => @ORM\Entity などとしておく。
     */
    $config = Setup::createAnnotationMetadataConfiguration(
        $settings['metadata_dirs'],
        $settings['dev_mode'],
        $proxyDir,
        $cache,
        false
    );

    $config->setProxyNamespace('App\ORM\Proxy');

    $logger = new DbalLogger($container->get('logger'));
    $config->setSQLLogger($logger);

    return EntityManager::create($settings['connection'], $config);
};

/**
 * session manager
 *
 * @return SessionManager
 */
$container['sm'] = static function ($container) {
    $settings = $container->get('settings')['session'];

    $config = new Laminas\Session\Config\SessionConfig();
    $config->setOptions($settings);

    return new SessionManager($config);
};

/**
 * Flash Messages
 *
 * @return FlashMessages
 */
$container['flash'] = static function ($container) {
    $session = $container->get('sm')->getContainer('flash');

    return new FlashMessages($session);
};

/**
 * auth
 *
 * @return Auth
 */
$container['auth'] = static function ($container) {
    return new Auth(
        $container->get('em'),
        $container->get('sm')->getContainer('auth')
    );
};

/**
 * Azure Blob Storage Client
 *
 * @link https://github.com/Azure/azure-storage-php/tree/master/azure-storage-blob
 *
 * @return BlobRestProxy
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

    return BlobRestProxy::createBlobService($connection);
};

$container['errorHandler'] = static function ($container) {
    return new Error(
        $container->get('logger'),
        $container->get('settings')['displayErrorDetails']
    );
};

$container['phpErrorHandler'] = static function ($container) {
    return new PhpError(
        $container->get('logger'),
        $container->get('settings')['displayErrorDetails']
    );
};

$container['notFoundHandler'] = static function ($container) {
    return new NotFound(
        $container->get('view')
    );
};

$container['notAllowedHandler'] = static function ($container) {
    return new NotAllowed(
        $container->get('view')
    );
};

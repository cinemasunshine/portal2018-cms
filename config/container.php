<?php
/**
 * container.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 */

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
    $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container->get('router'), $basePath));

    return $view;
};

/**
 * logger
 * 
 * @todo 環境毎に設定を変える
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
    
    // chromePHP
    $chromePhpSettings = $settings['chrome_php'];
    $logger->pushHandler(new Monolog\Handler\ChromePHPHandler(
        $chromePhpSettings['level']
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
    
    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
        $settings['metadata_dirs'],
        $settings['dev_mode']
    );
    
    return \Doctrine\ORM\EntityManager::create($settings['connection'], $config);
};

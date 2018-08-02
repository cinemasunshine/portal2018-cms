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

<?php
/**
 * settings.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 * 
 * @return array
 */

$settings = [];

$settings['displayErrorDetails'] = (APP_ENV === 'dev');
$settings['addContentLengthHeader'] = false;

// view
$settings['view'] = [
    'template_path' => APP_ROOT . '/template',
    'settings' => [
        'debug' => (APP_ENV === 'dev'),
        'cache' => APP_ROOT . '/cache/view',
    ],
];


// logger
$loggerLevel = (APP_ENV === 'dev')
             ? \Monolog\Logger::DEBUG
             : \Monolog\Logger::ERROR;

$settings['logger'] = [
    'name' => 'app',
    'chrome_php' => [
        'level' => $loggerLevel,
    ],
];

return $settings;

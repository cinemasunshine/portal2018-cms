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

// doctrine
$settings['doctrine'] = [
    'dev_mode' => (APP_ENV === 'dev'),
    'metadata_dirs' => [APP_ROOT . '/src/ORM/Entity'],
    
    'connection' => [
        'driver'   => 'pdo_mysql',
        'host'     => getenv('DB_HOST'),
        'port'     => getenv('DB_PORT'),
        'dbname'   => getenv('DB_NAME'),
        'user'     => getenv('DB_USER'),
        'password' => getenv('DB_PASSWORD'),
        'charset'  => 'utf8mb4',
    ],
];

// storage
$settings['storage'] = [
    'secure'  => true,
    'account' => [
        'name' => getenv('STORAGE_NAME'),
        'key'  => getenv('STORAGE_KEY'),
    ],
];

return $settings;

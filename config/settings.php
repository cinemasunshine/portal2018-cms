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
        'host'     => getenv('MYSQLCONNSTR_HOST'),
        'port'     => getenv('MYSQLCONNSTR_PORT'),
        'dbname'   => getenv('MYSQLCONNSTR_NAME'),
        'user'     => getenv('MYSQLCONNSTR_USER'),
        'password' => getenv('MYSQLCONNSTR_PASSWORD'),
        'charset'  => 'utf8mb4',
    ],
];

// storage
$settings['storage'] = [
    'secure'  => true,
    'account' => [
        'name' => getenv('CUSTOMCONNSTR_STORAGE_NAME'),
        'key'  => getenv('CUSTOMCONNSTR_STORAGE_KEY'),
    ],
];

return $settings;

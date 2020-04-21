<?php

/**
 * settings.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 *
 * @return array
 */

$settings = [];
$settings['displayErrorDetails'] = APP_DEBUG;
$settings['addContentLengthHeader'] = false;

// view
$settings['view'] = [
    'template_path' => APP_ROOT . '/template',
    'settings' => [
        'debug' => APP_DEBUG,
        'cache' => APP_ROOT . '/cache/view',
    ],
];

/**
 * session
 *
 * laminas-session configのオプションとして使用。
 *
 * @link https://docs.laminas.dev/laminas-session/config/
 */
$settings['session'] = [
    'name' => 'csadmin',
    'gc_maxlifetime' => 60 * 60 * 2, // SASAKI-360
    'gc_probability' => 1,
    'gc_divisor' => 1,
];

// logger
$getLoggerSetting = function () {
    $settings = [
        'name' => 'app',
    ];

    if (APP_DEBUG) {
        $settings['chrome_php'] = [
            'level' => \Monolog\Logger::DEBUG,
        ];
    }

    $settings['buffer'] = [
        'limit' => 0, // ひとまず無制限とする
    ];

    $settings['azure_blob_storage'] = [
        'level' => \Monolog\Logger::INFO,
        'container' => 'admin-log',
        'blob' => date('Ymd') . '.log',
    ];

    return $settings;
};

$settings['logger'] = $getLoggerSetting();

/**
 * doctrine
 *
 * @return array
 * @link https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/configuration.html#installation-and-configuration
 */
$getDoctrineSetting = function () {
    $settings = [
        /**
         * ビルドに影響するのでtrueにするのはローカルモードに限定しておく。
         *
         * truneの場合
         * * キャッシュはメモリ内で行われる（ArrayCache）
         * * Proxyオブジェクトは全てのリクエストで再作成される
         *
         * falseの場合
         * * 指定のキャッシュが使用されるかAPC、Xcache、Memcache、Redisの順で確認される
         * * Proxyクラスをコマンドラインから明示的に作成する必要がある
         */
        'dev_mode' => (APP_ENV === 'local'),

        'metadata_dirs' => [APP_ROOT . '/src/ORM/Entity'],

        'connection' => [
            'driver'   => 'pdo_mysql',
            'host'     => getenv('MYSQLCONNSTR_HOST'),
            'port'     => getenv('MYSQLCONNSTR_PORT'),
            'dbname'   => getenv('MYSQLCONNSTR_NAME'),
            'user'     => getenv('MYSQLCONNSTR_USER'),
            'password' => getenv('MYSQLCONNSTR_PASSWORD'),
            'charset'  => 'utf8mb4',
            'driverOptions'  => [],

            // @link https://m-p.backlog.jp/view/SASAKI-246
            'serverVersion' => '5.7',
        ],
    ];

    if (getenv('MYSQLCONNSTR_SSL') === 'true') {
        // https://docs.microsoft.com/ja-jp/azure/mysql/howto-configure-ssl
        $cafile = APP_ROOT . '/cert/BaltimoreCyberTrustRoot.crt.pem';
        $settings['connection']['driverOptions'][PDO::MYSQL_ATTR_SSL_CA] = $cafile;
    }

    return $settings;
};

$settings['doctrine'] = $getDoctrineSetting();


// storage
$getStorageSettings = function () {
    $settings = [
        'account_name' => getenv('CUSTOMCONNSTR_STORAGE_NAME'),
        'account_key' => getenv('CUSTOMCONNSTR_STORAGE_KEY'),
    ];

    $secure = getenv('CUSTOMCONNSTR_STORAGE_SECURE');
    $settings['secure'] = ($secure === 'false') ? false : true;

    $blobEndpoint = getenv('CUSTOMCONNSTR_STORAGE_BLOB_ENDPOINT');
    $settings['blob_endpoint'] = ($blobEndpoint) ?: null;

    $publicEndpoint = getenv('CUSTOMCONNSTR_STORAGE_PUBLIC_ENDOPOINT');
    $settings['public_endpoint'] = ($publicEndpoint) ?: null;

    return $settings;
};

$settings['storage'] = $getStorageSettings();

return $settings;

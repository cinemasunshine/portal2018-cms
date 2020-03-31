<?php
/**
 * settings.php
 *
 * @author Atsushi Okui <okui@motionpicture.jp>
 *
 * @return array
 */

$settings = [];

$isDebug = in_array(APP_ENV, ['dev', 'test']);

$settings['displayErrorDetails'] = $isDebug;
$settings['addContentLengthHeader'] = false;

// view
$settings['view'] = [
    'template_path' => APP_ROOT . '/template',
    'settings' => [
        'debug' => $isDebug,
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
$getLoggerSetting = function ($isDebug) {
    $settings = [
        'name' => 'app',
    ];

    if ($isDebug) {
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

$settings['logger'] = $getLoggerSetting($isDebug);

// doctrine
$getDoctrineSetting = function ($isDebug) {
    $settings = [
        'dev_mode' => $isDebug,
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

$settings['doctrine'] = $getDoctrineSetting($isDebug);


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

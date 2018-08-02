<?php
/**
 * settings.php
 * 
 * @author Atsushi Okui <okui@motionpicture.jp>
 * 
 * @return array
 */

return [
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false, // Allow the web server to send the content-length header
    
    'view' => [
        'template_path' => APP_ROOT . '/template',
        'settings' => [
            'debug' => true, // set to false in production
            'cache' => APP_ROOT . '/cache/view',
        ],
    ],
];

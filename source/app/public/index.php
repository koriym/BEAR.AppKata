<?php

declare(strict_types=1);

use MyVendor\MyProject\Bootstrap;

session_name('bear-app');
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https',
]);

// NOTE: Enable the following if you want to POST from external sites.
//$PUBLIC_HOSTS = ['example.com', 'www.example.com', 'stage.example.com'];
//if (in_array($_SERVER['HTTP_HOST'], $PUBLIC_HOSTS, true)) {
//    $cookieParams = session_get_cookie_params();
//    session_set_cookie_params(array_merge($cookieParams, [
//        'samesite' => 'None',
//        'secure' => true,
//    ]));
//}

require dirname(__DIR__) . '/autoload.php';

$environment = getenv('APP_CONTEXT') ?: (PHP_SAPI === 'cli-server' ? 'dev' : 'prod');
$apiType = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') ? 'hal-api-app' : 'html-app';
$context = ($environment === 'prod' ? 'prod-' : '') . $apiType;

exit((new Bootstrap())($context, $GLOBALS, $_SERVER));

//exit((new Bootstrap())(PHP_SAPI === 'cli-server' ? 'hal-app' : 'prod-hal-app', $GLOBALS, $_SERVER)); // old

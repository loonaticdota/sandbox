<?php

// 1. Ensure BASEPATH is defined
if (!defined('BASEPATH')) {
    define('BASEPATH', $config['basepath'] ?? dirname(__DIR__));
}

// 2. Define BASEURL
define('BASEURL', rtrim($config['url'] ?? '', '/') . '/');

// 3. Autoload Composer dependencies
if (file_exists(BASEPATH . '/vendor/autoload.php')) {
    require_once BASEPATH . '/vendor/autoload.php';
}

// 4. Load Core Task & Concurrency Utilities
require_once BASEPATH . '/core/task.php';

// 5. Session Initialization (only for web requests)
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 5. URL Parsing & Segments
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$requestUri = urldecode($requestUri ?? '/');

// Auto-detect base folder from index.php location (works in root OR subdirectories)
$scriptDir  = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseSubdir = trim($scriptDir, '/');
$cleanedUri = trim($requestUri, '/');

// Strip the subdirectory prefix only if it matches exactly or as a directory segment
if (!empty($baseSubdir)) {
    if ($cleanedUri === $baseSubdir) {
        $cleanedUri = '';
    } elseif (str_starts_with($cleanedUri, $baseSubdir . '/')) {
        $cleanedUri = substr($cleanedUri, strlen($baseSubdir) + 1);
    }
}

$segments = !empty($cleanedUri) ? explode('/', $cleanedUri) : [];
$request  = !empty($segments) ? implode('/', $segments) : ($config['default_request'] ?? 'home');

// 6. Route Dispatch (API vs Web) - Web requests only (CLI requests are handled by cli.php)
if (defined('WEB_REQUEST') || PHP_SAPI !== 'cli') {
    if (($segments[0] ?? '') === 'api') {
        if (file_exists(BASEPATH . '/core/api.php')) {
            require_once BASEPATH . '/core/api.php';
            $api = new Api();
            $api->initialize($request);
        } else {
            http_response_code(501);
            echo 'API module not implemented yet.';
        }
    } else {
        require_once BASEPATH . '/core/router.php';
        $router = new Router();
        $router->load($request);
    }
}

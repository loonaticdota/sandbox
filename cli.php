<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Access Denied: cli.php can only be executed via the command line.\n");
}

require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/core/bootstrap.php';

$route = $argv[1] ?? 'help';
$rawPayload = $argv[2] ?? '{}';
$params = json_decode($rawPayload, true) ?? [];

if ($route === 'help' || $route === '--help') {
    echo "=====================================================\n";
    echo " SANDBOX CLI RUNNER\n";
    echo "=====================================================\n";
    echo "Usage:\n";
    echo "  php cli.php <route> [json_arguments]\n\n";
    echo "Examples:\n";
    echo "  php cli.php home\n";
    echo "  php cli.php mail/send '{\"to\":\"user@example.com\"}'\n";
    echo "=====================================================\n";
    exit(0);
}

require_once BASEPATH . '/core/router.php';
$router = new Router();
$router->load($route, !empty($params) ? [$params] : []);


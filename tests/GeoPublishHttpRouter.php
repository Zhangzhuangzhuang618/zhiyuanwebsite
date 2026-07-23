<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use app\core\App;
use app\model\Database;

$databasePath = getenv('GEO_TEST_DATABASE_PATH') ?: '';
$tokenHash = getenv('GEO_TEST_TOKEN_SHA256') ?: '';
$siteUrl = getenv('GEO_TEST_SITE_URL') ?: '';
if ($databasePath === '' || $tokenHash === '' || $siteUrl === '') {
    http_response_code(500);
    echo 'Test server configuration is incomplete.';
    exit;
}

$config = [
    'app' => [
        'admin_path' => 'webadmini',
        'default_timezone' => 'Asia/Shanghai',
        'site_url' => $siteUrl,
    ],
    'database' => [
        'type' => 'sqlite',
        'database' => $databasePath,
        'prefix' => 'zw_',
    ],
    'geo_publish_api' => [
        'enabled' => true,
        'token_sha256' => $tokenHash,
        'max_body_bytes' => 1024 * 1024,
        'target_nav_id' => 11,
        'site_url' => $siteUrl,
    ],
];

$GLOBALS['config'] = $config;
date_default_timezone_set('Asia/Shanghai');
Database::init($config['database']);
(new App($config))->run();

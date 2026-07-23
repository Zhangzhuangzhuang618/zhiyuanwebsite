<?php
declare(strict_types=1);

final class HttpTestFailure extends RuntimeException {}

function assertHttp(bool $condition, string $message): void
{
    if (!$condition) throw new HttpTestFailure($message);
}

function request(string $url, string $method, array $headers = [], ?array $body = null): array
{
    $options = [
        'http' => [
            'ignore_errors' => true,
            'method' => $method,
            'header' => implode("\r\n", $headers),
            'timeout' => 5,
        ],
    ];
    if ($body !== null) {
        $options['http']['content'] = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    $result = file_get_contents($url, false, stream_context_create($options));
    $responseHeaders = $http_response_header ?? [];
    preg_match('/\s(\d{3})\s/', $responseHeaders[0] ?? '', $match);
    return [
        'status' => isset($match[1]) ? (int)$match[1] : 0,
        'body' => json_decode((string)$result, true),
    ];
}

function waitForServer(string $url): void
{
    for ($attempt = 0; $attempt < 50; $attempt++) {
        $socket = @fsockopen('127.0.0.1', (int)parse_url($url, PHP_URL_PORT), $errorCode, $errorMessage, 0.1);
        if (is_resource($socket)) {
            fclose($socket);
            return;
        }
        usleep(100000);
    }
    throw new HttpTestFailure('PHP test server did not start.');
}

$database = tempnam(sys_get_temp_dir(), 'geo-publish-http-');
if ($database === false) throw new RuntimeException('Unable to create temporary database.');
$pdo = new PDO('sqlite:' . $database, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('CREATE TABLE zw_cms_nav (id INTEGER, title TEXT, pid INTEGER, url_model TEXT, status INTEGER)');
$pdo->exec("INSERT INTO zw_cms_nav VALUES (7, '新闻资讯', 0, 'news', 1), (11, '搬家百科', 7, 'news', 1)");
$pdo->exec('CREATE TABLE zw_cms_article ('
    . 'id INTEGER,title TEXT,subtitle TEXT,content TEXT,sketch TEXT,seo_title TEXT,seo_keyword TEXT,'
    . 'seo_content TEXT,image TEXT,create_time TEXT,update_time TEXT,delete_time TEXT,nav_id INTEGER,'
    . 'nav_pid INTEGER,browse INTEGER,sort INTEGER,status INTEGER,state INTEGER,states INTEGER,model TEXT,'
    . 'type TEXT,link TEXT,target TEXT,rand INTEGER,lang TEXT)');
$pdo->exec((string)file_get_contents(dirname(__DIR__) . '/database/migrations/001_geo_publish_api.sql'));
$pdo = null;

$token = 'local-http-test-token-with-enough-entropy';
$port = random_int(18082, 18999);
$baseUrl = 'http://127.0.0.1:' . $port;
$environment = array_merge($_ENV, [
    'GEO_TEST_DATABASE_PATH' => $database,
    'GEO_TEST_TOKEN_SHA256' => hash('sha256', $token),
    'GEO_TEST_SITE_URL' => $baseUrl,
]);
$command = [PHP_BINARY, '-S', '127.0.0.1:' . $port, '-t', dirname(__DIR__) . '/public', __DIR__ . '/GeoPublishHttpRouter.php'];
$process = proc_open($command, [STDIN, ['file', '/dev/null', 'a'], ['file', '/dev/null', 'a']], $pipes, dirname(__DIR__), $environment);
if (!is_resource($process)) throw new RuntimeException('Unable to start PHP test server.');

try {
    waitForServer($baseUrl);
    $unauthorized = request($baseUrl . '/api/geo/v1/capabilities', 'GET');
    assertHttp($unauthorized['status'] === 401, 'Capabilities must require authentication.');
    assertHttp(($unauthorized['body']['error']['code'] ?? '') === 'AUTH_REQUIRED', 'Unexpected auth error.');

    $authHeaders = ['Accept: application/json', 'Authorization: Bearer ' . $token];
    $capabilities = request($baseUrl . '/api/geo/v1/capabilities', 'GET', $authHeaders);
    assertHttp($capabilities['status'] === 200, 'Capabilities request failed.');
    assertHttp(($capabilities['body']['publish'] ?? false) === true, 'Publish capability is unavailable.');

    $payload = [
        'schema_version' => 'zhiyuan-news-payload@1',
        'platform_code' => 'official_site',
        'title' => '广州企业搬迁如何规划人员车辆与物品交接流程',
        'summary' => '从需求确认、现场勘察到物品交接，说明企业搬迁的关键步骤。',
        'body_html' => '<h2>先确认搬迁范围</h2><p>企业应先整理物品清单和时间要求。</p>',
        'seo_keywords' => ['广州企业搬迁', '搬迁流程'],
        'meta_description' => '广州企业搬迁流程说明，涵盖需求确认、现场勘察和物品交接。',
    ];
    ksort($payload, SORT_STRING);
    $body = [
        'content_version_id' => '33333333-3333-4333-8333-333333333333',
        'payload_hash' => hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
        'payload' => $payload,
    ];
    $publishHeaders = array_merge($authHeaders, [
        'Content-Type: application/json',
        'Idempotency-Key: official-site-http-test-1',
        'X-Request-Id: official-site-http-test-1',
    ]);
    $created = request($baseUrl . '/api/geo/v1/publish', 'POST', $publishHeaders, $body);
    assertHttp($created['status'] === 201, 'First publication must return HTTP 201.');
    assertHttp(($created['body']['status'] ?? '') === 'published', 'Publication did not complete.');

    $replay = request($baseUrl . '/api/geo/v1/publish', 'POST', $publishHeaders, $body);
    assertHttp($replay['status'] === 200, 'Idempotent replay must return HTTP 200.');
    assertHttp($replay['body'] === $created['body'], 'Idempotent response changed.');

    $externalId = (string)$created['body']['external_id'];
    $status = request($baseUrl . '/api/geo/v1/status/' . rawurlencode($externalId), 'GET', $authHeaders);
    assertHttp($status['status'] === 200, 'Status request failed.');
    assertHttp($status['body'] === $created['body'], 'Status response differs from publish response.');

    $verification = new PDO('sqlite:' . $database, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    assertHttp((int)$verification->query('SELECT COUNT(*) FROM zw_cms_article')->fetchColumn() === 1, 'Replay created a duplicate article.');
    assertHttp((int)$verification->query('SELECT status FROM zw_cms_article')->fetchColumn() === 1, 'Article is not published.');
} finally {
    proc_terminate($process);
    proc_close($process);
    @unlink($database);
}

fwrite(STDOUT, "GeoPublishHttpTest passed\n");

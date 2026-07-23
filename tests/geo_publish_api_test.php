<?php
declare(strict_types=1);

$root = dirname(__DIR__);
$sourceDatabase = $root . '/data/demo.sqlite';
$testDatabase = sys_get_temp_dir() . '/zhiyuan-geo-publish-' . bin2hex(random_bytes(6)) . '.sqlite';
$logFile = sys_get_temp_dir() . '/zhiyuan-geo-publish-' . bin2hex(random_bytes(6)) . '.log';
$port = random_int(18100, 18999);
$baseUrl = 'http://127.0.0.1:' . $port;
$token = 'local-geo-test-token';

if (!copy($sourceDatabase, $testDatabase)) {
    throw new RuntimeException('无法创建临时 SQLite 副本');
}

$command = 'exec ' . escapeshellarg(PHP_BINARY)
    . ' -S 127.0.0.1:' . $port
    . ' -t ' . escapeshellarg($root . '/public')
    . ' ' . escapeshellarg($root . '/public/router.php');
$environment = array_merge(getenv(), [
    'GEO_PUBLISH_ENABLED' => '1',
    'GEO_PUBLISH_TOKEN_SHA256' => hash('sha256', $token),
    'GEO_PUBLISH_NEWS_NAV_ID' => '11',
    'ZHIYUAN_DATABASE_PATH' => $testDatabase,
    'ZHIYUAN_SITE_URL' => $baseUrl,
]);
$process = proc_open($command, [
    0 => ['file', '/dev/null', 'r'],
    1 => ['file', $logFile, 'a'],
    2 => ['file', $logFile, 'a'],
], $pipes, $root, $environment);
if (!is_resource($process)) {
    @unlink($testDatabase);
    throw new RuntimeException('无法启动 PHP 测试服务');
}

try {
    $ready = false;
    for ($attempt = 0; $attempt < 50; $attempt++) {
        usleep(100000);
        $probe = request('GET', $baseUrl . '/api/geo/v1/capabilities', [
            'Authorization: Bearer wrong-token',
        ]);
        if ($probe['status'] === 401) {
            $ready = true;
            break;
        }
    }
    assertTrue($ready, '测试服务未按时启动');

    $capabilities = request('GET', $baseUrl . '/api/geo/v1/capabilities', [
        'Authorization: Bearer ' . $token,
    ]);
    assertSame(200, $capabilities['status'], '能力探测应成功');
    assertSame(
        ['get_status' => true, 'metrics' => false, 'publish' => true],
        $capabilities['json'],
        '能力响应不符合契约'
    );

    $payload = [
        'body_html' => '<h2>企业搬家前要先确认哪些事项</h2><p>建议先完成资产清单、时间窗口和负责人确认，再安排现场勘察与车辆计划。</p>',
        'meta_description' => '广州企业搬家流程指南，说明前期清点、现场勘察、运输安排和验收要点。',
        'platform_code' => 'official_site',
        'schema_version' => 'zhiyuan-news-payload@1',
        'seo_keywords' => ['广州企业搬家', '企业搬家流程'],
        'summary' => '从清点、勘察、运输到验收，梳理广州企业搬家的关键执行步骤。',
        'title' => '广州企业搬家服务如何规划流程并降低停工风险指南',
    ];
    $body = [
        'content_version_id' => '11111111-1111-4111-8111-111111111111',
        'payload' => $payload,
        'payload_hash' => hash('sha256', stableJson($payload)),
    ];
    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Idempotency-Key: official-site:test:1111',
    ];

    $first = request('POST', $baseUrl . '/api/geo/v1/publish', $headers, stableJson($body));
    assertSame(201, $first['status'], '首次发布应创建文章');
    assertSame('published', $first['json']['status'] ?? null, '首次发布状态错误');
    $articleId = (string)($first['json']['external_id'] ?? '');
    assertTrue($articleId !== '', '首次发布未返回文章 ID');

    $repeat = request('POST', $baseUrl . '/api/geo/v1/publish', $headers, stableJson($body));
    assertSame(200, $repeat['status'], '幂等重试应返回既有文章');
    assertSame($first['json'], $repeat['json'], '幂等重试响应应保持一致');

    $status = request('GET', $baseUrl . '/api/geo/v1/status/' . $articleId, [
        'Authorization: Bearer ' . $token,
    ]);
    assertSame(200, $status['status'], '文章状态查询应成功');
    assertSame($first['json'], $status['json'], '文章状态响应应与发布响应一致');

    $pdo = new PDO('sqlite:' . $testDatabase);
    $articleCount = (int)$pdo->query(
        'SELECT COUNT(*) FROM zw_cms_article WHERE id = ' . (int)$articleId
    )->fetchColumn();
    $recordCount = (int)$pdo->query('SELECT COUNT(*) FROM zw_geo_publish_record')->fetchColumn();
    assertSame(1, $articleCount, '幂等重试不应重复创建文章');
    assertSame(1, $recordCount, '幂等重试不应重复创建发布记录');

    $changedPayload = $payload;
    $changedPayload['body_html'] = '<h2>企业搬家前要先确认哪些事项</h2><p>修改正文以验证幂等冲突。</p>';
    $changedBody = $body;
    $changedBody['payload'] = $changedPayload;
    $changedBody['payload_hash'] = hash('sha256', stableJson($changedPayload));
    $conflict = request(
        'POST',
        $baseUrl . '/api/geo/v1/publish',
        $headers,
        stableJson($changedBody)
    );
    assertSame(409, $conflict['status'], '同一幂等键的不同内容应冲突');
    assertSame('IDEMPOTENCY_CONFLICT', $conflict['json']['code'] ?? null, '冲突错误码错误');

    echo "GEO publish API integration test passed.\n";
} finally {
    proc_terminate($process);
    proc_close($process);
    @unlink($testDatabase);
    @unlink($logFile);
}

function request(string $method, string $url, array $headers, ?string $body = null): array
{
    $options = [
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $headers),
            'ignore_errors' => true,
            'timeout' => 2,
        ],
    ];
    if ($body !== null) {
        $options['http']['content'] = $body;
    }
    $raw = @file_get_contents($url, false, stream_context_create($options));
    $responseHeaders = $http_response_header ?? [];
    preg_match('/\s(\d{3})\s/', (string)($responseHeaders[0] ?? ''), $matches);
    $status = isset($matches[1]) ? (int)$matches[1] : 0;
    $json = is_string($raw) ? json_decode($raw, true) : null;
    return ['status' => $status, 'json' => $json];
}

function stableJson($value): string
{
    return json_encode(sortJson($value), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function sortJson($value)
{
    if (!is_array($value)) {
        return $value;
    }
    if ($value === [] || array_keys($value) === range(0, count($value) - 1)) {
        return array_map('sortJson', $value);
    }
    ksort($value, SORT_STRING);
    foreach ($value as $key => $child) {
        $value[$key] = sortJson($child);
    }
    return $value;
}

function assertSame($expected, $actual, string $message): void
{
    if ($expected !== $actual) {
        throw new RuntimeException($message . ': ' . var_export($actual, true));
    }
}

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use app\service\GeoNewsPublisher;
use app\service\GeoPublishException;

final class TestFailure extends RuntimeException {}

function expect(bool $condition, string $message): void
{
    if (!$condition) throw new TestFailure($message);
}

function expectError(callable $callable, string $code): void
{
    try {
        $callable();
    } catch (GeoPublishException $error) {
        expect($error->errorCode() === $code, 'Expected ' . $code . ', got ' . $error->errorCode());
        return;
    }
    throw new TestFailure('Expected error ' . $code . '.');
}

$database = tempnam(sys_get_temp_dir(), 'geo-publish-');
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

$GLOBALS['config'] = ['database' => ['prefix' => 'zw_']];
$token = 'test-token-with-enough-entropy';
$publisher = new GeoNewsPublisher($pdo, [
    'enabled' => true,
    'token_sha256' => hash('sha256', $token),
    'target_nav_id' => 11,
    'site_url' => 'https://example.test',
]);
$publisher->authenticate('Bearer ' . $token);
expectError(function () use ($publisher): void {
    $publisher->authenticate('Bearer wrong');
}, 'AUTH_INVALID');

$payload = [
    'schema_version' => 'zhiyuan-news-payload@1',
    'platform_code' => 'official_site',
    'title' => '广州企业搬迁如何规划人员车辆与物品交接流程',
    'summary' => '从需求确认、现场勘察到物品交接，说明企业搬迁的关键步骤。',
    'body_html' => '<h2>先确认搬迁范围</h2><p>企业应先整理物品清单和时间要求。</p>',
    'seo_keywords' => ['广州企业搬迁', '搬迁流程'],
    'meta_description' => '广州企业搬迁流程说明，涵盖需求确认、现场勘察和物品交接。',
];
$request = [
    'content_version_id' => '11111111-1111-4111-8111-111111111111',
    'payload_hash' => hash('sha256', GeoNewsPublisher::canonicalJson($payload)),
    'payload' => $payload,
];
$first = $publisher->publish($request, 'publish-test-1');
expect($first['created'] === true, 'First request must create an article.');
expect($first['response']['external_id'] === '1', 'Unexpected article id.');
expect((int)$pdo->query('SELECT COUNT(*) FROM zw_cms_article')->fetchColumn() === 1, 'Article was not inserted.');
expect((int)$pdo->query('SELECT nav_id FROM zw_cms_article')->fetchColumn() === 11, 'Wrong category.');

$replay = $publisher->publish($request, 'publish-test-1');
expect($replay['created'] === false, 'Replay must not create another article.');
expect($replay['response'] === $first['response'], 'Replay response changed.');
expect((int)$pdo->query('SELECT COUNT(*) FROM zw_cms_article')->fetchColumn() === 1, 'Replay duplicated article.');

$secondKey = $publisher->publish($request, 'publish-test-2');
expect($secondKey['created'] === false, 'Same content version must replay across keys.');
expect((int)$pdo->query('SELECT COUNT(*) FROM zw_cms_article')->fetchColumn() === 1, 'Second key duplicated article.');

$changed = $request;
$changed['payload']['summary'] = '不同摘要会形成不同的有效载荷。';
$changed['payload_hash'] = hash('sha256', GeoNewsPublisher::canonicalJson($changed['payload']));
expectError(function () use ($publisher, $changed): void {
    $publisher->publish($changed, 'publish-test-1');
}, 'IDEMPOTENCY_CONFLICT');

$unsafe = $request;
$unsafe['content_version_id'] = '22222222-2222-4222-8222-222222222222';
$unsafe['payload']['body_html'] = '<script>alert(1)</script><p>正文</p>';
$unsafe['payload_hash'] = hash('sha256', GeoNewsPublisher::canonicalJson($unsafe['payload']));
expectError(function () use ($publisher, $unsafe): void {
    $publisher->publish($unsafe, 'publish-test-unsafe');
}, 'REQUEST_INVALID');

$status = $publisher->status('1');
expect($status === $first['response'], 'Status response differs from publication response.');
expectError(function () use ($publisher): void {
    $publisher->status('999');
}, 'RESOURCE_NOT_FOUND');

@unlink($database);
fwrite(STDOUT, "GeoNewsPublisherTest passed\n");

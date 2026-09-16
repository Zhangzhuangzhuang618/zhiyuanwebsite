<?php
// Isolated browser-test server. Never loads production config or business data.
$directory = getenv('CASE_EDITOR_TEST_DIR');
if (!$directory || !is_dir($directory)) { http_response_code(500); exit('Missing test directory'); }
$root = dirname(__DIR__);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (strpos($path, '/static/') === 0) return false;
if (preg_match('#^/upload/[0-9]{8}/[a-f0-9]{32}\.(jpg|png|gif|webp)$#', $path)) {
    $file = $directory . $path;
    if (!is_file($file)) { http_response_code(404); exit; }
    header('Content-Type: ' . mime_content_type($file)); readfile($file); exit;
}
require $root . '/vendor/autoload.php';
define('VIEW_PATH', $path === '/test-detail' ? $directory . '/views/' : $root . '/app/view/');
define('UPLOAD_PATH', $directory . '/upload/');
define('RUNTIME_PATH', $directory . '/');
define('__LANG__', 'zh-cn');
$config = ['app' => ['admin_path' => 'test-admin'], 'database' => ['type' => 'sqlite', 'database' => $directory . '/test.sqlite', 'prefix' => 'zw_'], 'admin' => ['username' => 'test', 'password_hash' => password_hash('test-password', PASSWORD_DEFAULT)], 'upload' => ['max_size' => 1048576]];
\app\model\Database::init($config['database']);
$pdo = \app\model\Database::getInstance();
$pdo->exec('CREATE TABLE IF NOT EXISTS zw_cms_article (id INTEGER, status INTEGER, delete_time INTEGER)');
$pdo->exec('CREATE TABLE IF NOT EXISTS zw_cms_message (id INTEGER, title TEXT, phone TEXT, status INTEGER, create_time INTEGER, delete_time INTEGER)');
$pdo->exec('CREATE TABLE IF NOT EXISTS zw_cms_nav (id INTEGER, title TEXT, sort INTEGER)');
$pdo->exec('CREATE TABLE IF NOT EXISTS zw_cms_cases (id INTEGER PRIMARY KEY,title TEXT,nav_id INTEGER,sketch TEXT,image TEXT,content TEXT,seo_title TEXT,seo_keyword TEXT,seo_content TEXT,sort INTEGER,status INTEGER,state INTEGER,states INTEGER,update_time INTEGER,create_time INTEGER,delete_time INTEGER,browse INTEGER,lang TEXT)');
if (!$pdo->query('SELECT COUNT(*) FROM zw_cms_cases')->fetchColumn()) {
    $pdo->exec("INSERT INTO zw_cms_cases (id,title,content,status,sort) VALUES (1,'旧案例','<p>原始正文 &amp; 特殊字符</p><p><img src=\"/static/home/images/icon_line.jpg\" /></p>',0,0)");
}
if ($path === '/test-detail') {
    @mkdir(VIEW_PATH . 'layout', 0755, true);
    file_put_contents(VIEW_PATH . 'layout/header.php', '<!doctype html><html lang="zh-CN"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><body>');
    file_put_contents(VIEW_PATH . 'layout/footer.php', '</body></html>');
    file_put_contents(VIEW_PATH . 'layout/news_right.php', '');
    $detail = $pdo->query('SELECT * FROM zw_cms_cases WHERE id=1')->fetch();
    $assetUrl = function ($url) { return $url; };
    $banner = '/static/home/images/icon_line.jpg';
    $classify = $about_news = []; $prev = $next = null;
    include $root . '/app/view/cases/detail.php';
    exit;
}
(new \app\core\App($config))->run();

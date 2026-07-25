<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use app\service\BaiduUrlPush;

function baiduExpect(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
}

$requests = [];
$push = new BaiduUrlPush([
    'enabled' => true,
    'api_url' => 'http://data.zz.baidu.com/urls?site=https%3A%2F%2Fwww.example.test&token=test-token',
], function (string $apiUrl, string $url) use (&$requests): array {
    $requests[] = [$apiUrl, $url];
    return ['status' => 200, 'body' => '{"success":1,"remain":99}'];
});

$result = $push->submit('https://www.example.test/detail/news1.html');
baiduExpect($result['success'] === true, 'Successful API response must be accepted.');
baiduExpect($result['submitted'] === 1, 'Submitted count is incorrect.');
baiduExpect(count($requests) === 1, 'Expected exactly one request.');

$disabled = new BaiduUrlPush();
baiduExpect($disabled->submit('https://www.example.test/detail/news1.html')['attempted'] === false, 'Disabled push must not make a request.');

$rejected = new BaiduUrlPush([
    'enabled' => true,
    'api_url' => 'http://data.zz.baidu.com/urls?site=https%3A%2F%2Fwww.example.test&token=test-token',
], function (): array {
    return ['status' => 401, 'body' => '{"error":401}'];
});
baiduExpect($rejected->submit('https://www.example.test/detail/news1.html')['success'] === false, 'Rejected API response must fail safely.');

fwrite(STDOUT, "BaiduUrlPushTest passed\n");

<?php
/**
 * PHP内置服务器路由脚本
 * 处理.html后缀和URL重写
 */

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// 如果请求的是真实文件，直接返回
if ($path !== '/' && file_exists(__DIR__ . $path)) {
    return false;
}

// 否则全部交给 index.php 处理
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';

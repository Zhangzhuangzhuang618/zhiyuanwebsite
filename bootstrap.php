<?php
/**
 * 志远搬家官网 - 应用引导文件
 */

// 定义路径常量
define('ROOT_PATH', __DIR__ . '/');
define('APP_PATH', ROOT_PATH . 'app/');
define('RUNTIME_PATH', ROOT_PATH . 'runtime/');
define('PUBLIC_PATH', ROOT_PATH . 'public/');
define('CONFIG_PATH', APP_PATH . 'config/');
define('VIEW_PATH', APP_PATH . 'view/');
define('UPLOAD_PATH', PUBLIC_PATH . 'upload/');

// 自动加载
require_once ROOT_PATH . 'vendor/autoload.php';

// 加载配置
$config = require CONFIG_PATH . 'site.php';
$GLOBALS['config'] = $config;
date_default_timezone_set($config['app']['default_timezone'] ?? 'Asia/Shanghai');

// 生产环境不向访客输出 PHP 错误；需要排查时仅将 site.php 的 debug 临时设为 true。
error_reporting(E_ALL);
ini_set('display_errors', !empty($config['app']['debug']) ? '1' : '0');
ini_set('log_errors', '1');

// 初始化数据库连接
\app\model\Database::init($config['database']);

// 获取当前城市（从子域名解析）
function getCurrentCity() {
    $host = $_SERVER['HTTP_HOST'] ?? 'www.zhiyuanbj.cn';
    $parts = explode('.', $host);
    if (count($parts) == 3 && $parts[0] != 'www') {
        $cityDomains = $GLOBALS['config']['city_domains'] ?? [];
        foreach ($cityDomains as $city) {
            if ($city['en_mark'] === $parts[0]) {
                return $city['mark'];
            }
        }
    }
    return '';
}

// 获取当前语言
function getCurrentLang() {
    // 简化：默认中文
    return 'zh-cn';
}

// 定义城市和语言常量
define('__CITY__', getCurrentCity());
define('__LANG__', getCurrentLang());

// 获取站点配置缓存
function siteConfig($key = null) {
    static $siteConfig = null;
    if ($siteConfig === null) {
        $configModel = new \app\model\SystemConfig();
        $siteConfig = $configModel->getAllConfig();
    }
    if ($key === null) {
        return $siteConfig;
    }
    // SEO标题中的城市替换
    $value = $siteConfig[$key] ?? '';
    if (in_array($key, ['seo_title', 'seo_keyword', 'seo_content']) && __CITY__) {
        $value = str_replace('$city', __CITY__, $value);
    }
    return $value;
}

// 启动应用
$app = new \app\core\App($config);
$app->run();

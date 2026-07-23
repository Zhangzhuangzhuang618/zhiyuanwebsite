<?php
/**
 * 环境配置模板。
 * 复制为 app/config/site.php 后，填入数据库与后台账号配置；该文件不应提交到仓库。
 */

$env = static function (string $name, $default = null) {
    $value = getenv($name);
    return $value === false || $value === '' ? $default : $value;
};

return [
    'app' => [
        'debug' => false,
        'default_timezone' => 'Asia/Shanghai',
        'site_name' => '广州志远搬家服务有限公司',
        'site_url' => $env('ZHIYUAN_SITE_URL', 'https://www.zhiyuanbj.cn'),
        'admin_path' => 'webadmini',
    ],
    'admin' => [
        'username' => 'admin',
        // 在命令行执行 password_hash('你的新密码', PASSWORD_DEFAULT) 后填入结果。
        'password_hash' => '',
    ],
    'geo_publish_api' => [
        'enabled' => filter_var(getenv('GEO_PUBLISH_API_ENABLED') ?: '0', FILTER_VALIDATE_BOOLEAN),
        // 仅配置令牌 SHA-256；原始令牌只保存在 GEO Content OS 的加密平台账号凭证中。
        'token_sha256' => getenv('GEO_PUBLISH_TOKEN_SHA256') ?: '',
        'max_body_bytes' => 1024 * 1024,
        'target_nav_id' => (int)(getenv('GEO_PUBLISH_TARGET_NAV_ID') ?: 11),
        'site_url' => 'https://www.zhiyuanbj.cn',
    ],
    'database' => [
        'type' => 'sqlite',
        'hostname' => '',
        'database' => $env('ZHIYUAN_DATABASE_PATH', __DIR__ . '/../../data/demo.sqlite'),
        'username' => '',
        'password' => '',
        'hostport' => '',
        'charset' => 'utf8',
        'prefix' => 'zw_',
    ],
    'geo_publish' => [
        'enabled' => $env('GEO_PUBLISH_ENABLED', '0') === '1',
        // 原始令牌只交给 GEO Content OS；官网仅保存 SHA-256 哈希。
        'token_sha256' => strtolower((string)$env('GEO_PUBLISH_TOKEN_SHA256', '')),
        'news_nav_id' => (int)$env('GEO_PUBLISH_NEWS_NAV_ID', 11),
    ],
    'lang' => ['default' => 'zh-cn', 'list' => ['zh-cn', 'en-us']],
    'cache' => ['type' => 'file', 'path' => __DIR__ . '/../runtime/cache/', 'expire' => 3600],
    'email' => [
        'host' => 'smtp.example.com', 'port' => 465, 'username' => '', 'password' => '',
        'from' => '', 'from_name' => '志远搬家', 'char_set' => 'UTF-8', 'smtp_secure' => 'ssl',
    ],
    'captcha' => ['width' => 150, 'height' => 50, 'length' => 4, 'font_size' => 20],
    'upload' => [
        'path' => __DIR__ . '/../public/upload/', 'max_size' => 10 * 1024 * 1024,
        'ext' => 'jpg,jpeg,png,gif,bmp,webp,mp4,pdf,doc,docx,xls,xlsx,zip,rar',
    ],
    'city_domains' => [
        ['mark' => '天河', 'en_mark' => 'tianhe', 'domain' => 'tianhe.zhiyuanbj.cn'],
        ['mark' => '海珠', 'en_mark' => 'haizhu', 'domain' => 'haizhu.zhiyuanbj.cn'],
        ['mark' => '白云', 'en_mark' => 'baiyun', 'domain' => 'baiyun.zhiyuanbj.cn'],
        ['mark' => '番禺', 'en_mark' => 'panyu', 'domain' => 'panyu.zhiyuanbj.cn'],
        ['mark' => '越秀', 'en_mark' => 'yuexiu', 'domain' => 'yuexiu.zhiyuanbj.cn'],
        ['mark' => '荔湾', 'en_mark' => 'liwan', 'domain' => 'liwan.zhiyuanbj.cn'],
        ['mark' => '黄埔', 'en_mark' => 'huangpu', 'domain' => 'huangpu.zhiyuanbj.cn'],
        ['mark' => '增城', 'en_mark' => 'zengcheng', 'domain' => 'zengcheng.zhiyuanbj.cn'],
        ['mark' => '南沙', 'en_mark' => 'nansha', 'domain' => 'nansha.zhiyuanbj.cn'],
        ['mark' => '从化', 'en_mark' => 'conghua', 'domain' => 'conghua.zhiyuanbj.cn'],
        ['mark' => '花都', 'en_mark' => 'huadu', 'domain' => 'huadu.zhiyuanbj.cn'],
    ],
    'service_cities' => [
        '广州' => '18924177677', '东莞' => '18924177677', '佛山' => '18924177677',
        '肇庆' => '18924177677', '江门' => '18924177677',
    ],
];

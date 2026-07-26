<?php
/**
 * 志远搬家官网 - 环境配置
 *
 * 运行环境：PHP 7.3+ / MySQL 5.7
 * 运行目录：/public
 */

$env = static function (string $name, $default = null) {
    $value = getenv($name);
    return $value === false || $value === '' ? $default : $value;
};

return [
    // 应用基本配置
    'app' => [
        'debug'          => false,
        'default_timezone' => 'Asia/Shanghai',
        'site_name'      => '广州志远搬家服务有限公司',
        'site_url'       => $env('ZHIYUAN_SITE_URL', 'https://www.zhiyuanbj.cn'),
        'admin_path'     => 'webadmini',
    ],

    // 后台初始账号。密码仅保存哈希值；上线前请替换为新的 password_hash() 结果。
    'admin' => [
        'username'      => 'admin',
        'password_hash' => '$2y$10$vLZmeWHttnuBb7efe5KQZuY2nh7RwiulJjeKacJrTJhRu6BN6B/k2',
    ],

    // 主数据库配置（演示环境使用SQLite，正式环境切换为MySQL）
    'database' => [
        'type'     => 'sqlite',
        'hostname' => '',
        'database' => $env('ZHIYUAN_DATABASE_PATH', __DIR__ . '/../../data/demo.sqlite'),
        'username' => '',
        'password' => '',
        'hostport' => '',
        'charset'  => 'utf8',
        'prefix'   => 'zw_',
    ],

    // GEO Content OS 专用新闻发布 API。官网只保存原始令牌的 SHA-256。
    'geo_publish_api' => [
        'enabled'        => filter_var($env('GEO_PUBLISH_API_ENABLED', '0'), FILTER_VALIDATE_BOOLEAN),
        'token_sha256'   => strtolower((string)$env('GEO_PUBLISH_TOKEN_SHA256', '')),
        'max_body_bytes' => 1024 * 1024,
        'target_nav_id'  => (int)$env('GEO_PUBLISH_TARGET_NAV_ID', 11),
        'site_url'       => $env('ZHIYUAN_SITE_URL', 'https://www.zhiyuanbj.cn'),
    ],
    /*
    // MySQL配置（正式环境使用）
    'database' => [
        'type'     => 'mysql',
        'hostname' => '127.0.0.1',
        'database' => 'zhiyuan_com',
        'username' => 'zhiyuan_com',
        'password' => '',
        'hostport' => '3306',
        'charset'  => 'utf8mb4',
        'prefix'   => 'zw_',
    ],
    */

    // 多语言配置
    'lang' => [
        'default' => 'zh-cn',
        'list'    => ['zh-cn', 'en-us'],
    ],

    // 缓存配置
    'cache' => [
        'type'   => 'file',
        'path'   => __DIR__ . '/../runtime/cache/',
        'expire' => 3600,
    ],

    // 邮件配置
    'email' => [
        'host'       => 'smtp.example.com',
        'port'       => 465,
        'username'   => '',
        'password'   => '',
        'from'       => '',
        'from_name'  => '志远搬家',
        'char_set'   => 'UTF-8',
        'smtp_secure'=> 'ssl',
    ],

    // 验证码配置
    'captcha' => [
        'width'    => 150,
        'height'   => 50,
        'length'   => 4,
        'font_size'=> 20,
    ],

    // 上传配置
    'upload' => [
        'path'      => __DIR__ . '/../public/upload/',
        'max_size'  => 10 * 1024 * 1024, // 10MB
        'ext'       => 'jpg,jpeg,png,gif,bmp,webp,mp4,pdf,doc,docx,xls,xlsx,zip,rar',
    ],

    // SEO城市子域名配置
    'city_domains' => [
        ['mark' => '天河', 'en_mark' => 'tianhe',   'domain' => 'tianhe.zhiyuanbj.cn'],
        ['mark' => '海珠', 'en_mark' => 'haizhu',   'domain' => 'haizhu.zhiyuanbj.cn'],
        ['mark' => '白云', 'en_mark' => 'baiyun',   'domain' => 'baiyun.zhiyuanbj.cn'],
        ['mark' => '番禺', 'en_mark' => 'panyu',    'domain' => 'panyu.zhiyuanbj.cn'],
        ['mark' => '越秀', 'en_mark' => 'yuexiu',   'domain' => 'yuexiu.zhiyuanbj.cn'],
        ['mark' => '荔湾', 'en_mark' => 'liwan',    'domain' => 'liwan.zhiyuanbj.cn'],
        ['mark' => '黄埔', 'en_mark' => 'huangpu',  'domain' => 'huangpu.zhiyuanbj.cn'],
        ['mark' => '增城', 'en_mark' => 'zengcheng','domain' => 'zengcheng.zhiyuanbj.cn'],
        ['mark' => '南沙', 'en_mark' => 'nansha',   'domain' => 'nansha.zhiyuanbj.cn'],
        ['mark' => '从化', 'en_mark' => 'conghua',  'domain' => 'conghua.zhiyuanbj.cn'],
        ['mark' => '花都', 'en_mark' => 'huadu',    'domain' => 'huadu.zhiyuanbj.cn'],
    ],

    // 服务城市（页面内展示用）
    'service_cities' => [
        '广州' => '18924177677',
        '东莞' => '18924177677',
        '佛山' => '18924177677',
        '肇庆' => '18924177677',
        '江门' => '18924177677',
    ],
];

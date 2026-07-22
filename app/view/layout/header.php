<!DOCTYPE html>
<?php
$seoTitle = $page_title ?? '广州志远搬家服务有限公司';
$seoDescription = $page_description ?? '广州志远搬家服务有限公司提供同城搬家、跨市搬家、企业搬迁及相关搬运服务，具体服务方案以实际沟通确认结果为准。';
$ogImage = !empty($page_image) ? $page_image : ($site['logo'] ?? '');
$navById = [];
foreach ($nav as $navItem) {
    $navById[(int) $navItem['id']] = $navItem;
}
$navHref = static function (int $id) use ($navById): string {
    $item = $navById[$id] ?? [];
    return $item['href'] ?: '/' . ($item['url_model'] ?? 'index') . '.html';
};
$productHref = static fn (int $id): string => '/detail/products' . $id . '.html';
$headerMenu = [
    ['key' => 'home', 'title' => '首页', 'href' => $navHref(1)],
    ['key' => 'personal', 'title' => '个人搬家', 'href' => $navHref(2), 'children' => [
        ['title' => '同城搬家', 'href' => $navHref(2)], ['title' => '跨市搬家', 'href' => $navHref(3)], ['title' => '出国搬家', 'href' => $navHref(4)],
        ['title' => '日式搬家', 'href' => $productHref(15)], ['title' => '收纳整理', 'href' => $productHref(18)], ['title' => '家居拆装', 'href' => $productHref(19)],
    ]],
    ['key' => 'business', 'title' => '企业搬迁', 'href' => $productHref(2), 'children' => [
        ['title' => '办公室搬迁', 'href' => $productHref(2)], ['title' => '院校搬迁', 'href' => $productHref(3)],
        ['title' => '实验室搬迁', 'href' => $productHref(6)], ['title' => '图书馆搬迁', 'href' => $productHref(7)], ['title' => '工厂搬迁', 'href' => $productHref(8)],
        ['title' => '跨市办公室搬迁', 'href' => $productHref(10)], ['title' => '跨市院校搬迁', 'href' => $productHref(11)], ['title' => '大型工厂搬迁', 'href' => $productHref(17)], ['title' => '高空吊装', 'href' => $productHref(5)],
    ]],
    ['key' => 'valuables', 'title' => '贵重物品搬运', 'href' => $productHref(16), 'children' => [
        ['title' => '钢琴搬运', 'href' => $productHref(16)], ['title' => '设备搬迁', 'href' => $productHref(4)], ['title' => '艺术品搬运', 'href' => $productHref(20)],
    ]],
    ['key' => 'about', 'title' => '关于志远', 'href' => $navHref(5)],
    ['key' => 'cases', 'title' => '服务案例', 'href' => $navHref(6)],
    ['key' => 'news', 'title' => '新闻资讯', 'href' => $navHref(7)],
    ['key' => 'contact', 'title' => '联系志远', 'href' => $navHref(8)],
];
$headerPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$activeHeaderKey = 'home';
if (preg_match('#^/products/(2|3|4)\.html$#', $headerPath)) {
    $activeHeaderKey = 'personal';
} elseif (preg_match('#^/products/21\.html$#', $headerPath)) {
    $activeHeaderKey = 'valuables';
} elseif (preg_match('#^/detail/products(\d+)\.html$#', $headerPath, $productMatch)) {
    $productKeyMap = [2 => 'business', 3 => 'business', 5 => 'business', 6 => 'business', 7 => 'business', 8 => 'business', 10 => 'business', 11 => 'business', 17 => 'business', 4 => 'valuables', 16 => 'valuables', 20 => 'valuables', 15 => 'personal', 18 => 'personal', 19 => 'personal'];
    $activeHeaderKey = $productKeyMap[(int) $productMatch[1]] ?? 'personal';
} elseif (str_starts_with($headerPath, '/about')) {
    $activeHeaderKey = 'about';
} elseif (str_starts_with($headerPath, '/cases')) {
    $activeHeaderKey = 'cases';
} elseif (str_starts_with($headerPath, '/news') || str_starts_with($headerPath, '/detail/news')) {
    $activeHeaderKey = 'news';
} elseif (str_starts_with($headerPath, '/contact')) {
    $activeHeaderKey = 'contact';
}
?>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="keywords" content="<?= htmlspecialchars($page_keywords ?? '广州搬家,同城搬家,跨市搬家,企业搬迁,搬家服务', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="canonical" href="<?= htmlspecialchars($canonical_url, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($seoDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonical_url, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:type" content="<?= !empty($page_type) ? htmlspecialchars($page_type, ENT_QUOTES, 'UTF-8') : 'website' ?>">
    <?php if ($ogImage): ?><meta property="og:image" content="<?= htmlspecialchars(preg_match('#^https?://#i', $ogImage) ? $ogImage : 'https://www.zhiyuanbj.cn' . $ogImage, ENT_QUOTES, 'UTF-8') ?>"><?php endif; ?>
    <?php foreach ($structured_data as $schema): ?>
    <script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
    <?php endforeach; ?>
    <link rel="shortcut icon" type="image/x-icon" href="/upload/20250316/993a52a682b5711b1a4c03e7ce22ea03.png" media="screen">
    <link rel="stylesheet" href="/static/home/css/swiper.css">
    <link rel="stylesheet" href="/static/home/css/animate.min.css">
    <link rel="stylesheet" href="/static/home/css/mediaelementplayer.css">
    <link rel="stylesheet" href="/static/home/css/global.css">
    <link rel="stylesheet" href="/static/home/css/head.css">
    <link rel="stylesheet" href="/static/home/css/header.css">
    <link rel="stylesheet" href="/static/home/css/footer.css">
    <link rel="stylesheet" href="/static/home/css/media.css">
    <script src="/static/home/js/jquery.min.js"></script>
</head>
<body>
<!-- 头部开始 -->
<header class="clearfix">
    <div class="header-top">
        <div class="center">
            <div class="header-top-left">
                <div class="city" id="city-toggle">
                    <a href="https://www.zhiyuanbj.cn"><i id="city-now"></i> <span>[切换]</span></a>
                    <div class="city-box">
                        <div class="city-nav-1"><ul><li class="active">热门城市</li></ul></div>
                        <div class="city-con">
                            <div class="city-list-1"><ul><li><p>
                                <?php foreach ($city_list as $city): ?>
                                <a href="http://<?= $city['en_mark'] ?>.zhiyuanbj.cn" city-code="<?= $city['en_mark'] ?>"><?= $city['mark'] ?></a>
                                <?php endforeach; ?>
                                <p><a href="https://www.zhiyuanbj.cn">更多&gt;</a></p>
                            </li></ul></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-top-right">
                <ul>
                    <li><a href="tel:02085627757">在线客服</a></li>
                    <li><a href="/contact.html">联系志远</a></li>
                    <li class="top-tel"><span>咨询热线：<a href="tel:<?= preg_replace('/\D+/', '', (string) $site['phone']) ?>"><?= $site['phone'] ?></a></span></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="header-nav clearfix">
        <div class="center">
            <div class="header-left">
                <img src="<?= $site['logo'] ?: '/upload/20250316/343c6ff6bcb0d1dd7a9a4989741d35ea.png' ?>" alt="<?= htmlspecialchars($site['name'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <!--APP导航 -->
            <span class="menuBtn"></span>
            <nav id="m_nav" class="hd-r common" aria-label="移动端主导航">
                <div class="content">
                    <div class="nav_m_box">
                        <ul class="m_nav_list">
                        <?php foreach ($headerMenu as $item): ?>
                            <li class="title <?= !empty($item['children']) ? 'has-children' : '' ?>">
                                <h3 class="tit"><a class="v1" href="<?= $item['href'] ?>" title="<?= $item['title'] ?>"><?= $item['title'] ?></a>
                                <?php if (!empty($item['children'])): ?><i></i><?php endif; ?></h3>
                                <div class="list dl1">
                                    <?php foreach ($item['children'] ?? [] as $sub): ?>
                                    <dd class="">
                                        <a href="<?= $sub['href'] ?>" class="v2"><?= $sub['title'] ?></a>
                                    </dd>
                                    <?php endforeach; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </nav>
            <section class="nav_mask"></section>
            <!-- PC导航 -->
            <nav class="nav-line" aria-label="主导航">
                <ul class="nav">
                    <?php foreach ($headerMenu as $item): ?>
                    <li class="nav-item <?= !empty($item['children']) ? 'nav-dropdown' : '' ?>" data-nav-key="<?= $item['key'] ?>">
                        <a href="<?= $item['href'] ?>" class="nav-parent"><?= $item['title'] ?><?php if (!empty($item['children'])): ?><span class="nav-caret" aria-hidden="true"></span><?php endif; ?></a>
                        <?php if (!empty($item['children'])): ?>
                        <ul class="nav-submenu">
                            <?php foreach ($item['children'] as $sub): ?><li><a href="<?= $sub['href'] ?>"><?= $sub['title'] ?></a></li><?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="line"></div>
            </nav>
        </div>
    </div>
</header>
<!-- 头部结束 -->
<span id="PActive" class="none"><?= $p_active ?? 0 ?></span>
<span id="SActive" class="none"><?= $s_active ?? '' ?></span>
<div class="alert"></div>
<div class="header-box"></div>

<script>
  $('.nav-line .nav li[data-nav-key="<?= $activeHeaderKey ?>"]').addClass('active');
</script>
<main id="main-content">

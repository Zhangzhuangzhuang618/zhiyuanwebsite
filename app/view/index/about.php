<?php include VIEW_PATH . 'layout/header.php'; ?>
<link rel="stylesheet" href="<?= htmlspecialchars($assetUrl('/static/home/css/about.css'), ENT_QUOTES, 'UTF-8') ?>">
<div class="page-banner"><img src="<?= htmlspecialchars($assetUrl($banner), ENT_QUOTES, 'UTF-8') ?>" class="w100 block" alt=""></div>
<div><div class="about_nav_2"><ul class="center clearfix"><?php foreach ($classify as $item): ?><a href="<?= $item['href'] ?: '/about/' . $item['id'] . '.html' ?>" target="<?= $item['target'] ?: '_self' ?>"><li class="df ac jc"><span class="icon" style="background-image: url(<?= $item['icon'] ?>);"></span><span><?= $item['title'] ?></span></li></a><?php endforeach; ?></ul></div></div>
<div><div class="wow fadeInUp" data-wow-delay="0.1s"><div class="center"><div class="title-top clearfix mt45"><p><?= $page['title'] ?><span><?= $page['subtitle'] ?></span></p></div></div><div class="about-container pd50 center mt20 border-radius-5 fs-14"><div class="clearfix"><?php if (($page['id'] ?? 0) === 13): ?><div class="fs-24 text-center"><h1 class="a-title">广州志远搬家服务有限公司</h1></div><?php endif; ?><div class="line-h-2"><?= html_entity_decode($page['content'] ?? '') ?></div></div></div></div></div>
<?php if ((int)($page['id'] ?? 0) === 13): ?>
<section id="dispatch-assurance" style="scroll-margin-top:120px"><?php include VIEW_PATH . 'layout/direct_service.php'; ?></section>
<section class="center boxsh pd20 mt25" style="line-height:1.9"><h2 class="fs-24">日式搬家与居民搬家服务</h2><p>志远提供半日式与日式精品搬家。半日式280元/立方米、5立方米起；精品日式320元/立方米、10立方米起。两种服务均含旧家打包收纳、包装材料、小家具拆装和装卸运输，精品日式增加新家还原与指定位置摆放。</p><p><a href="/detail/products15.html">查看日式搬家服务范围</a>　<a href="/pricing.html">查看车型套餐与增项说明</a></p></section>
<?php endif; ?>
<?php include VIEW_PATH . 'layout/footer.php'; ?>

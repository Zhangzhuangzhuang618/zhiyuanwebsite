<?php include VIEW_PATH . 'layout/header.php'; ?>
<link rel="stylesheet" href="/static/home/css/about.css">
<div class="page-banner"><img src="<?= $banner ?>" class="w100 block" alt=""></div>
<div><div class="about_nav_2"><ul class="center clearfix"><?php foreach ($classify as $item): ?><a href="<?= $item['href'] ?: '/about/' . $item['id'] . '.html' ?>" target="<?= $item['target'] ?: '_self' ?>"><li class="df ac jc"><span class="icon" style="background-image: url(<?= $item['icon'] ?>);"></span><span><?= $item['title'] ?></span></li></a><?php endforeach; ?></ul></div></div>
<div><div class="wow fadeInUp" data-wow-delay="0.1s"><div class="center"><div class="title-top clearfix mt45"><p><?= $page['title'] ?><span><?= $page['subtitle'] ?></span></p></div></div><div class="about-container pd50 center mt20 border-radius-5 fs-14"><div class="clearfix"><?php if (($page['id'] ?? 0) === 13): ?><div class="fs-24 text-center"><p class="a-title">志远搬家连锁品牌</p></div><?php endif; ?><div class="line-h-2"><?= html_entity_decode($page['content'] ?? '') ?></div></div></div></div></div>
    <?php include VIEW_PATH . 'layout/footer.php'; ?>

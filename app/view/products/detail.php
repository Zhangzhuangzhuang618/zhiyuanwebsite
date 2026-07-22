<?php include VIEW_PATH . 'layout/header.php'; ?>
<link rel="stylesheet" href="/static/home/css/index.css"><link rel="stylesheet" href="/static/home/css/products.css">
<div class="page_banner"><img src="<?= $banner ?>" class="w100 block" alt=""></div>
<div class="center clearfix"><div class="wow fadeInUp" data-wow-delay="0.1s"><div class="top-number mt45" style="background: url(<?= $HFF['image'] ?>);"><ul class="df ac"><?php foreach ($HFF['child_id'] as $index => $item): ?><li class="df ac"><p><img src="<?= $item['icon1'] ?>" alt=""></p><div><h2 class="fs-18"><span id="count<?= $index + 5 ?>"><?= $item['subtitle'] ?></span><?= $item['sketch'] ?></h2><p class="fs-14"><?= $item['title'] ?></p></div></li><?php endforeach; ?></ul></div></div></div>
<?php include VIEW_PATH . 'layout/page_quote.php'; ?>
<div class="wow fadeInUp" data-wow-delay="0.1s"><div class="center"><div class="title-top clearfix mt45"><p>业务介绍<span>Business Introduction</span></p></div></div><div class="about-container pd50 center mt20 border-radius-5 fs-14"><div class="clearfix"><div class="fs-24 text-center"><p class="a-title"><?= $detail['title'] ?></p></div><div class="mt30 line-h-2"><?= html_entity_decode($detail['content'] ?? '') ?></div></div></div></div>
<?php include VIEW_PATH . 'layout/product_sections.php'; ?>
<?php include VIEW_PATH . 'layout/footer.php'; ?>

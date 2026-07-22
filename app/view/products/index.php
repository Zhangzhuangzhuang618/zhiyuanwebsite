<?php include VIEW_PATH . 'layout/header.php'; ?>
<link rel="stylesheet" href="/static/home/css/index.css"><link rel="stylesheet" href="/static/home/css/products.css">
<div class="page_banner"><img src="<?= $banner ?>" class="w100 block" alt=""></div>
<div class="center clearfix"><div class="wow fadeInUp" data-wow-delay="0.1s"><div class="top-number mt45" style="background: url(<?= $HFF['image'] ?>);"><ul class="df ac"><?php foreach ($HFF['child_id'] as $index => $item): ?><li class="df ac"><p><img src="<?= $item['icon1'] ?>" alt=""></p><div><h2 class="fs-18"><span id="count<?= $index + 5 ?>"><?= $item['subtitle'] ?></span><?= $item['sketch'] ?></h2><p class="fs-14"><?= $item['title'] ?></p></div></li><?php endforeach; ?></ul></div></div></div>
<?php include VIEW_PATH . 'layout/page_quote.php'; ?>
<div class="center clearfix"><div class="wow fadeInUp" data-wow-delay="0.1s"><div class="plate-top clearfix"><p>多元化的业务范围 <span>Diversified Business Scope</span></p></div><div class="s product-lines stop-swiping"><div class="swer"><div class="swiide"><?php foreach ($all as $item): ?><div class="slide-box"><div class="img-box"><img class="lazy" data-original="<?= $item['image'] ?>" alt="" width="100%"></div><p class="title14"><?= $item['title'] ?></p><p class="infos wline2"><?= $item['subtitle'] ?></p><p class="cont"><a href="<?= $item['link'] ?>"><span>了解详情</span></a><a class="getBaojia_tc"><span>获取报价</span></a></p></div><?php endforeach; ?></div></div></div></div></div>
<?php include VIEW_PATH . 'layout/product_sections.php'; ?>
<?php include VIEW_PATH . 'layout/footer.php'; ?>

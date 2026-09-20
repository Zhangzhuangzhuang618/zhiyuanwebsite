<?php include VIEW_PATH . 'layout/header.php'; ?>
<link rel="stylesheet" href="<?= htmlspecialchars($assetUrl('/static/home/css/index.css'), ENT_QUOTES, 'UTF-8') ?>"><link rel="stylesheet" href="<?= htmlspecialchars($assetUrl('/static/home/css/products.css'), ENT_QUOTES, 'UTF-8') ?>">
<div class="page_banner"><img src="<?= htmlspecialchars($assetUrl($banner), ENT_QUOTES, 'UTF-8') ?>" class="w100 block" alt=""></div>
<div class="wow fadeInUp" data-wow-delay="0.1s"><div class="center"><div class="title-top clearfix mt45"><p>业务介绍<span>Business Introduction</span></p></div></div><div class="about-container pd50 center mt20 border-radius-5 fs-14"><div class="clearfix"><div class="fs-24 text-center"><h1 class="a-title"><?= htmlspecialchars($detail['title'], ENT_QUOTES, 'UTF-8') ?></h1></div><div class="mt30 line-h-2"><?= html_entity_decode($detail['content'] ?? '') ?></div></div></div></div>
<?php include VIEW_PATH . 'layout/direct_service.php'; ?>
<?php include VIEW_PATH . 'layout/product_sections.php'; ?>
<?php include VIEW_PATH . 'layout/footer.php'; ?>

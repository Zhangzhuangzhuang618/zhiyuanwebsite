<?php include VIEW_PATH . 'layout/header.php'; ?>
<link rel="stylesheet" href="/static/home/css/about.css">
<div class="page-banner"><img src="<?= htmlspecialchars($banner ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w100 block" alt="媒体报道"></div>
<div class="about_nav_2"><ul class="center clearfix"><?php foreach ($classify as $item): ?><a href="<?= htmlspecialchars($item['href'] ?: '/about/' . $item['id'] . '.html', ENT_QUOTES, 'UTF-8') ?>" target="<?= htmlspecialchars($item['target'] ?: '_self', ENT_QUOTES, 'UTF-8') ?>"><li class="df ac jc<?= (int) ($item['id'] ?? 0) === (int) ($page['id'] ?? 0) ? ' on' : '' ?>"><span class="icon" style="background-image:url(<?= htmlspecialchars($item['icon'] ?? '', ENT_QUOTES, 'UTF-8') ?>);"></span><span><?= htmlspecialchars($item['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></li></a><?php endforeach; ?></ul></div>
<div class="center"><div class="title-top clearfix mt45"><p><?= htmlspecialchars($page['title'] ?? '媒体报道', ENT_QUOTES, 'UTF-8') ?><span><?= htmlspecialchars($page['subtitle'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></p></div></div>
<main class="about-container center mt20 mb60 border-radius-5 fs-14 clearfix">
    <ul class="media-list mt30">
        <?php foreach ($mediaReports as $report): ?>
            <?php $link = html_entity_decode($report['link'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            <li class="clearfix"><a class="img-box" href="<?= htmlspecialchars($link ?: '#', ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><img src="<?= htmlspecialchars($report['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($report['title'] ?? '志远搬家媒体报道', ENT_QUOTES, 'UTF-8') ?>"></a><div class="text-box"><a href="<?= htmlspecialchars($link ?: '#', ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><h3 class="fs-18 mb15"><?= htmlspecialchars($report['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3></a><p class="fs-14 line-h-2 c666"><?= htmlspecialchars($report['sketch'] ?? '', ENT_QUOTES, 'UTF-8') ?></p></div></li>
        <?php endforeach; ?>
    </ul>
</main>
<?php include VIEW_PATH . 'layout/footer.php'; ?>

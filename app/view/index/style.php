<?php include VIEW_PATH . 'layout/header.php'; ?>
<link rel="stylesheet" href="/static/home/css/about.css">
<div class="page-banner"><img src="<?= htmlspecialchars($banner ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w100 block" alt="员工风采"></div>
<div class="about_nav_2"><ul class="center clearfix"><?php foreach ($classify as $item): ?><a href="<?= htmlspecialchars($item['href'] ?: '/about/' . $item['id'] . '.html', ENT_QUOTES, 'UTF-8') ?>" target="<?= htmlspecialchars($item['target'] ?: '_self', ENT_QUOTES, 'UTF-8') ?>"><li class="df ac jc<?= (int) ($item['id'] ?? 0) === (int) ($page['id'] ?? 0) ? ' on' : '' ?>"><span class="icon" style="background-image:url(<?= htmlspecialchars($item['icon'] ?? '', ENT_QUOTES, 'UTF-8') ?>);"></span><span><?= htmlspecialchars($item['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></li></a><?php endforeach; ?></ul></div>
<div class="center"><div class="title-top clearfix mt45"><p><?= htmlspecialchars($page['title'] ?? '员工风采', ENT_QUOTES, 'UTF-8') ?><span><?= htmlspecialchars($page['subtitle'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></p></div></div>
<main class="about-container center mt20 mb60 border-radius-5 fs-14 clearfix">
    <ul class="photo_list clearfix mt30">
        <?php foreach ($staffPhotos as $photo): ?>
            <li><a class="block" href="<?= htmlspecialchars($photo['image'] ?? '#', ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><div class="img"><img src="<?= htmlspecialchars($photo['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($photo['title'] ?? '志远搬家员工风采', ENT_QUOTES, 'UTF-8') ?>"></div><p><?= htmlspecialchars($photo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></p></a></li>
        <?php endforeach; ?>
    </ul>
</main>
<?php include VIEW_PATH . 'layout/footer.php'; ?>

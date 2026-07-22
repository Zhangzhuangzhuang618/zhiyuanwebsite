<?php include VIEW_PATH . 'layout/header.php'; ?>
<link rel="stylesheet" href="/static/home/css/about.css">
<div class="page-banner"><img src="<?= htmlspecialchars($banner ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w100 block" alt="企业文化"></div>
<div class="about_nav_2"><ul class="center clearfix"><?php foreach ($classify as $item): ?><a href="<?= htmlspecialchars($item['href'] ?: '/about/' . $item['id'] . '.html', ENT_QUOTES, 'UTF-8') ?>" target="<?= htmlspecialchars($item['target'] ?: '_self', ENT_QUOTES, 'UTF-8') ?>"><li class="df ac jc<?= (int) ($item['id'] ?? 0) === (int) ($page['id'] ?? 0) ? ' on' : '' ?>"><span class="icon" style="background-image:url(<?= htmlspecialchars($item['icon'] ?? '', ENT_QUOTES, 'UTF-8') ?>);"></span><span><?= htmlspecialchars($item['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></li></a><?php endforeach; ?></ul></div>
<div class="center"><div class="title-top clearfix mt45"><p><?= htmlspecialchars($page['title'] ?? '企业文化', ENT_QUOTES, 'UTF-8') ?><span><?= htmlspecialchars($page['subtitle'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></p></div></div>
<main class="about-container center mt20 border-radius-5 fs-14 clearfix">
    <section class="Culture-video mt50">
        <div class="img-box"><div class="video-box"><img src="<?= htmlspecialchars($cultureProfile['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($cultureProfile['title'] ?? '企业文化', ENT_QUOTES, 'UTF-8') ?>"></div></div>
        <div class="text-box">
            <p class="fs-18 fw-b mb20"><?= htmlspecialchars($cultureProfile['title'] ?? '企业文化', ENT_QUOTES, 'UTF-8') ?></p>
            <p class="fs-14 line-h-2 c666"><?= nl2br(htmlspecialchars($cultureProfile['sketch'] ?? $cultureProfile['subtitle'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
    </section>
    <section class="mt60">
        <div class="title-top clearfix"><p>服务理念<span>Service concept</span></p></div>
        <ul class="cultrue-box clearfix mt30">
            <?php foreach ($serviceConcepts as $concept): ?>
                <li class="mb20"><div class="round"><img src="<?= htmlspecialchars($concept['icon1'] ?? '', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($concept['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div><div class="text"><p class="fs-18 mb10"><?= htmlspecialchars($concept['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></p><p class="fs-14 line-h-15 c666"><?= htmlspecialchars($concept['subtitle'] ?? '', ENT_QUOTES, 'UTF-8') ?></p></div></li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="Culture-video mt50 mb60">
        <div class="img-box"><div class="video-box"><img src="<?= htmlspecialchars($managementConcept['image'] ?? '', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($managementConcept['title'] ?? '经营理念', ENT_QUOTES, 'UTF-8') ?>"></div></div>
        <div class="text-box">
            <p class="fs-18 fw-b mb20"><?= htmlspecialchars($managementConcept['title'] ?? '经营理念', ENT_QUOTES, 'UTF-8') ?></p>
            <p class="fs-14 line-h-2 c666"><?= nl2br(htmlspecialchars($managementConcept['sketch'] ?? $managementConcept['subtitle'] ?? '', ENT_QUOTES, 'UTF-8')) ?></p>
        </div>
    </section>
</main>
<?php include VIEW_PATH . 'layout/footer.php'; ?>

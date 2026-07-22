<?php include VIEW_PATH . 'layout/header.php'; ?>
<link rel="stylesheet" href="/static/home/css/news.css">
<div class="page-banner"><img src="<?= $banner ?>" class="w100 block" alt="新闻资讯"></div>
<nav class="column-menu center boxsh pd20 mt25 wow fadeInUp" data-wow-delay="0.1s" aria-label="资讯分类">
    <div class="border-bottom"><div class="column-menu-list clearfix"><p><a>资讯类型：</a></p><p class="clearfix column-menu-list-nav"><?php foreach ($classify as $item): ?><a href="<?= $item['href'] ?: '/news/' . $item['id'] . '.html' ?>" target="<?= $item['target'] ?: '_self' ?>"><?= $item['title'] ?></a><?php endforeach; ?></p></div></div>
</nav>
<div class="case-container center mt25 clearfix position-relative">
    <div class="left">
        <article class="news-article boxsh wow fadeInUp" data-wow-delay="0.1s">
            <header class="title mt10">
                <h1><?= htmlspecialchars($detail['title'], ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="c888 mt10 fs-12"><span class="mr20">内容来源：广州志远搬家服务有限公司</span><time datetime="<?= date('c', (int) $detail['create_time']) ?>">更新时间：<?= date('Y-m-d H:i:s', (int) $detail['create_time']) ?></time></p>
            </header>
            <div class="article-box mt25 fs-14 pb20"><?= html_entity_decode($detail['content'] ?? '') ?></div>
            <nav class="prev-next over-hidden border-top pt20 line-height-2" aria-label="文章翻页">
                <div class="fl w40"><p class="fs-14 ellipsis c888"><?php if ($prev): ?>上一篇：<a href="/detail/news<?= $prev['id'] ?>.html"><?= $prev['title'] ?></a><?php else: ?>上一篇：没有更多了<?php endif; ?></p></div>
                <div class="fr w40 text-right"><p class="fs-14 ellipsis c888"><?php if ($next): ?>下一篇：<a href="/detail/news<?= $next['id'] ?>.html"><?= $next['title'] ?></a><?php else: ?>下一篇：没有更多了<?php endif; ?></p></div>
            </nav>
        </article>
        <section class="recommend boxsh mt25 wow fadeInUp" data-wow-delay="0.1s" aria-labelledby="related-news-title">
            <div class="title clearfix"><h2 id="related-news-title" class="fl">相关资讯</h2></div>
            <div class="recommend-read pd20 fs-14 line-height-2 over-hidden"><ul><?php foreach ($about_news as $item): ?><li class="ellipsis"><a href="<?= $item['link'] ?: '/detail/news' . $item['id'] . '.html' ?>" target="<?= $item['target'] ?: '_self' ?>"><?= $item['title'] ?></a></li><?php endforeach; ?></ul></div>
        </section>
    </div>
    <?php include VIEW_PATH . 'layout/news_right.php'; ?>
</div>
<script src="/static/home/js/news.js"></script>
<?php include VIEW_PATH . 'layout/footer.php'; ?>

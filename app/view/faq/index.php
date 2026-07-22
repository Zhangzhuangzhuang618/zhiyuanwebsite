<?php include VIEW_PATH . 'layout/header.php'; ?>
<link rel="stylesheet" href="/static/home/css/list.css">
<div class="center clearfix mt20">
    <nav class="breadcrumb" aria-label="面包屑"><a href="/">首页</a> &gt; <span>搬家常见问题</span></nav>
    <article class="page-content mt20 boxsh pd30">
        <header>
            <h1 class="fs-24 mb20">广州搬家常见问题</h1>
            <p class="c888 line-h-2">以下内容说明搬家前常见的费用、预约、打包、拆装和搬运问题。实际服务方案应以地址、物品和现场条件的确认结果为准。</p>
        </header>
        <section class="mt20" aria-label="搬家常见问题解答">
            <?php foreach ($faqs as $faq): ?>
            <section class="pt20 pb20" style="border-bottom:1px solid #eee;">
                <h2 class="fs-18 mb10"><?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8') ?></h2>
                <p class="c666 line-h-2"><?= htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8') ?></p>
            </section>
            <?php endforeach; ?>
        </section>
    </article>
</div>
<?php include VIEW_PATH . 'layout/footer.php'; ?>

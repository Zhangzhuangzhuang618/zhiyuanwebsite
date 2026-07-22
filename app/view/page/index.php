<?php include VIEW_PATH . 'layout/header.php'; ?>
<link rel="stylesheet" href="/static/home/css/list.css">

<div class="center clearfix mt20">
    <div class="breadcrumb"><a href="/">首页</a> &gt; <span><?= $page['title'] ?? '页面' ?></span></div>
    <div class="page-content mt20 boxsh pd30">
        <h1 class="fs-20 mb20"><?= $page['title'] ?? '' ?></h1>
        <div class="detail-body"><?= $content ?? $page['content'] ?? '' ?></div>
    </div>
</div>

<?php include VIEW_PATH . 'layout/footer.php'; ?>

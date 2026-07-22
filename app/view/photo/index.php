<?php include VIEW_PATH . 'layout/header.php'; ?>
<link rel="stylesheet" href="/static/home/css/list.css">

<div class="center">
    <div class="breadcrumb mt20"><a href="/">首页</a> &gt; <span><?= $currentNav['title'] ?? '相册展示' ?></span></div>

    <div class="list-container mt20">
        <?php if (!empty($list)): ?>
        <div class="grid-list clearfix">
            <?php foreach ($list as $item): ?>
            <div class="grid-item boxsh">
                <div class="img-box"><img src="<?= $item['image'] ?>" alt="<?= $item['title'] ?>" width="100%" style="cursor:pointer;" onclick="showBigImage('<?= $item['image'] ?>')"></div>
                <div class="text-box"><h3><?= $item['title'] ?></h3></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if ($totalPage > 1): ?>
        <div class="pagination mt30">
            <?php for ($i = 1; $i <= $totalPage; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="empty">暂无图片</div>
        <?php endif; ?>
    </div>
</div>

<script>
function showBigImage(src) {
    var html = '<div style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.8);z-index:9999;display:flex;align-items:center;justify-content:center;" onclick="this.remove()">';
    html += '<img src="'+src+'" style="max-width:90%;max-height:90%;">';
    html += '</div>';
    $('body').append(html);
}
</script>

<?php include VIEW_PATH . 'layout/footer.php'; ?>

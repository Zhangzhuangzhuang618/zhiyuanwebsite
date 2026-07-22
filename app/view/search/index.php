<?php include VIEW_PATH . 'layout/header.php'; ?>
<link rel="stylesheet" href="/static/home/css/list.css">

<div class="center clearfix mt20">
    <div class="search-header boxsh pd30">
        <form action="/search.html" method="get" class="flex">
            <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="请输入关键词搜索..." class="form-input flex-1" style="height:44px;font-size:16px;padding:0 15px;border:2px solid #e52f22;border-radius:4px 0 0 4px;">
            <button type="submit" class="dominant-bg-color" style="padding:0 30px;color:#fff;border:none;border-radius:0 4px 4px 0;cursor:pointer;font-size:16px;">搜索</button>
        </form>
    </div>

    <?php if (!empty($keyword)): ?>
    <div class="search-results mt20 boxsh pd30">
        <h3>搜索 "<?= htmlspecialchars($keyword) ?>" 的结果</h3>

        <?php
        $allResults = array_merge($results['products'] ?? [], $results['news'] ?? []);
        if (!empty($allResults)):
        ?>
        <ul class="search-list mt20">
            <?php foreach ($allResults as $item): ?>
            <li class="mt15 pb15" style="border-bottom:1px solid #eee;">
                <?php $url = $item['type'] === 'product' ? '/detail/products' . $item['id'] . '.html' : '/detail/news' . $item['id'] . '.html'; ?>
                <a href="<?= $url ?>"><h4><?= $item['title'] ?></h4></a>
                <p class="c888 mt5"><?= mb_substr(strip_tags($item['sketch'] ?? ''), 0, 150) ?></p>
                <span class="tag"><?= $item['type'] === 'product' ? '服务' : '新闻' ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <p class="c888 mt20">未找到相关内容，请尝试其他关键词。</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include VIEW_PATH . 'layout/footer.php'; ?>

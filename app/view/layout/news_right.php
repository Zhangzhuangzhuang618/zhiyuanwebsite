<div class="right">
  <div class="form-box boxsh clearfix fs-14 wow fadeInUp" data-wow-delay="0.1s">
    <section class="quote-entry" aria-labelledby="sidebar-quote-title">
      <p class="quote-entry-kicker">新版参考报价</p>
      <h2 id="sidebar-quote-title">搬家价格计算器</h2>
      <p>选择出发地、目的地、车型、距离、物品体积和大件，立即查看参考价格。</p>
      <button type="button" class="pianoBtn getBaojia">开始价格估算</button>
      <small>支持半日式搬家与日式精品搬家报价</small>
    </section>
  </div>
  <div class="kf mt25 boxsh wow fadeInUp" data-wow-delay="0.1s"><a href="tel:02085627757"><img class="lazy" data-original="/static/home/images/kf.jpg" alt="" src="/static/home/images/kf.jpg" style="display: inline;"></a></div>
  <div class="recommend mt25 boxsh wow fadeInUp" data-wow-delay="0.1s"><div class="recommendKs"><div class="title"><h3>搬家常见问题</h3></div><div class="recommend-list"><?php foreach ($CJWT['child_id'] as $item): ?><a href="/faq.html"><?= $item['title'] ?></a><?php endforeach; ?></div></div></div>
  <div class="suspension">
    <div class="recommend mt25 boxsh wow fadeInUp" data-wow-delay="0.1s"><div class="title clearfix"><h3 class="fl">热门搬家服务</h3><p class="more fr fs-14"><a href="/products.html">查看更多<span>›</span></a></p></div><div class="pd20"><div class="swiper-container recommend-case swiper-container-horizontal"><div class="swiper-wrapper"><?php foreach ($left_hot as $item): ?><div class="swiper-slide swiper-slide-active"><div class="img-box"><a href="<?= $item['link'] ?: '/detail/products' . $item['id'] . '.html' ?>" target="<?= $item['target'] ?: '_self' ?>"><img class="lazy" data-original="<?= $item['image'] ?>" alt="" src="<?= $item['image'] ?>" style="display: block;"></a></div><div class="text-box mt15"><h3 class="ellipsis"><a href="<?= $item['link'] ?: '/detail/products' . $item['id'] . '.html' ?>" target="<?= $item['target'] ?: '_self' ?>"><?= $item['title'] ?></a></h3><p class="mt10 infos wline2"><?= $item['subtitle'] ?> <a href="<?= $item['link'] ?: '/detail/products' . $item['id'] . '.html' ?>" target="<?= $item['target'] ?: '_self' ?>" class="danger-color">详情&gt;&gt;</a></p></div></div><?php endforeach; ?></div><div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets"></div></div></div></div>
    <div class="recommend mt25 boxsh wow fadeInUp" data-wow-delay="0.1s"><div class="title clearfix"><h3 class="fl">热门资讯</h3><p class="more fr fs-14"><a href="/news.html">查看更多<span>›</span></a></p></div><div class="question-list"><?php foreach ($ranking_news as $index => $item): ?><div class="list ellipsis"><i><?= $index + 1 ?></i><a href="<?= $item['link'] ?: '/detail/news' . $item['id'] . '.html' ?>" target="<?= $item['target'] ?: '_self' ?>"><?= $item['title'] ?></a></div><?php endforeach; ?></div></div>
  </div>
</div>

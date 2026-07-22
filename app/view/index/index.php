<?php include VIEW_PATH . 'layout/header.php'; ?>

<link rel="stylesheet" href="/static/home/css/index.css">

<!-- Banner -->
<section aria-label="首页横幅">
    <div class="swiper-container banner">
        <div class="swiper-wrapper">
            <?php if (!empty($banners)): ?>
                <?php foreach ($banners as $banner): ?>
                <div class="swiper-slide">
                    <img src="<?= $banner['image'] ?>" class="w100 block" alt="广州志远搬家服务横幅">
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="swiper-slide">
                    <img src="/upload/20260601/5687dee1d2a3aa94c741847c9d0f887e.jpg" class="w100 block" alt="广州志远搬家服务横幅">
                </div>
                <div class="swiper-slide">
                    <img src="/upload/20250216/091bcd172c24c0bf1d66320fcc2b3c80.jpg" class="w100 block" alt="广州志远搬家服务横幅">
                </div>
            <?php endif; ?>
        </div>
        <div class="swiper-pagination"></div>
        <div class="banner-btn banner-left"></div>
        <div class="banner-btn banner-next"></div>
    </div>
</section>

<section class="center clearfix mt20" aria-labelledby="home-answer-title">
    <div class="boxsh pd20">
        <h1 id="home-answer-title" class="fs-24 mb10">广州搬家服务：同城、跨市与企业搬迁</h1>
        <p class="c666 line-h-2">志远搬家提供广州同城搬家、跨市搬家、企业搬迁、家具拆装等搬运服务。服务方案和费用会结合地址、楼层、电梯、物品及现场条件确认，预约前可先说明主要需求获取清晰安排。</p>
    </div>
</section>

<!-- 多元化的业务范围 -->
<section class="center clearfix" aria-labelledby="service-scope-title">
    <div class="wow fadeInUp" data-wow-delay="0.1s">
        <div class="plate-top clearfix">
            <h2 id="service-scope-title">广州志远搬家提供哪些搬运服务？ <span>Diversified Business Scope</span></h2>
        </div>
        <div class="s product-lines stop-swiping">
            <div class="swer">
                <div class="swiide">
                    <?php foreach ($services as $service): ?>
                    <div class="slide-box">
                        <div class="img-box">
                            <img class="lazy" data-original="<?= $service['image'] ?>" alt="<?= htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8') ?>服务" width="100%">
                        </div>
                        <p class="title14"><?= $service['title'] ?></p>
                        <p class="infos wline2"><?= $service['sketch'] ?></p>
                        <p class="cont">
                            <a href="<?= $service['link'] ?: '/products/' . $service['nav_id'] . '.html' ?>" target="<?= $service['target'] ?? '_self' ?>"><span>了解详情</span></a>
                            <a class="getBaojia_tc"><span>获取报价</span></a>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 关于志远 -->
<section class="center clearfix" aria-labelledby="about-title">
    <div class="wow fadeInUp" data-wow-delay="0.1s">
        <div class="title-top clearfix mt30">
            <h2 id="about-title">关于志远 <span>About ZhongRen</span></h2>
            <p class="more"><a href="/about.html">查看更多<span>›</span></a></p>
        </div>
        <div class="about clearfix mt20 boxsh">
            <div class="top-text clearfix">
                <h3><?= $about['sketch'] ?? '提供更加优质、高效、安全的搬家搬厂搬设备等一切搬运服务' ?></h3>
                <p class="c888 fs-13 l-s-1">
                    <?= html_entity_decode($about['content'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
            <div class="about-nav center mt20 border-radius" id="about-md">
                <ul>
                    <?php if (!empty($aboutNavs)): ?>
                    <?php foreach ($aboutNavs as $aboutItem): ?>
                    <a href="/about/<?= $aboutItem['id'] ?>.html" target="_self">
                        <li>
                            <p><i class="icon" style="background-image: url(<?= $aboutItem['icon'] ?? '' ?>)"></i></p>
                            <p class="mt10 fs-14"><?= $aboutItem['title'] ?></p>
                        </li>
                    </a>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="index-hot-city mt20">
                <?php foreach ($service_cities as $cityName => $cityPhone): ?>
                <p><?= $cityName ?> <a href="tel:<?= preg_replace('/\D+/', '', (string) $cityPhone) ?>"><?= $cityPhone ?></a></p>
                <?php endforeach; ?>
                <p class="main-color"><a href="/contact.html">更多服务城市 &gt;&gt;</a></p>
            </div>
        </div>
    </div>
</section>

<!-- 选择我们的理由 -->
<section class="center clearfix" aria-labelledby="reason-title">
    <div class="wow fadeInUp" data-wow-delay="0.1s">
        <div class="title-top clearfix mt45">
            <h2 id="reason-title"><?= $why['title'] ?? '选择我们的理由' ?><span><?= $why['subtitle'] ?? 'Reasons for choosing us' ?></span></h2>
        </div>
        <div class="reasons pl-0 pr-0 mt20 boxsh over-hidden">
            <div class="left-content df js fc">
                <?php foreach (array_slice($reasons, 0, 3) as $i => $reason): ?>
                <div class="list list-left df ac" data-list="<?= $i ?>">
                    <div class="text text-right flex-1">
                        <h3 class="fs-16 mb10"><?= $reason['title'] ?? '' ?></h3>
                        <p class="c888 fs-13 line-h-1-7"><?= $reason['sketch'] ?? '' ?></p>
                    </div>
                    <span class="ico fr">
                        <img src="<?= $reason['icon1'] ?? '' ?>" class="icon_img icon1" alt="">
                        <img src="<?= $reason['icon2'] ?? '' ?>" class="icon_img icon1_hover" alt="">
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="center-content">
                <div class="top_video">
                    <div class="img_box">
                        <img src="<?= $why['image'] ?? '/upload/20250113/b815bc9d8f1b315d4e2bf6285eb9433b.jpg' ?>" alt="<?= htmlspecialchars($why['title'] ?? '志远搬家服务', ENT_QUOTES, 'UTF-8') ?>">
                        <span class="video_btn"></span>
                        <div class="img_c"></div>
                    </div>
                    <div class="hide">
                        <video src="/upload/20260717/zhiyuan-service.mp4" width="100%" height="100%" controls preload="metadata"></video>
                    </div>
                </div>
                <div class="bottom_baojia">
                    <img src="/static/home/images/r_img_01.png" alt="">
                    <div class="mt25">
                        <p class="fs-20 df ac">免费获取报价 <span class="hot">HOT</span></p>
                        <p class="mt10 mb10">30秒算一算搬家要花多少钱</p>
                        <p class="zixun-online getBaojia">立即获取</p>
                    </div>
                </div>
            </div>
            <div class="right-content df js fc">
                <?php foreach (array_slice($reasons, 3, 3) as $i => $reason): ?>
                <div class="list list-right df ac" data-list="<?= $i + 3 ?>">
                    <span class="ico fr">
                        <img src="<?= $reason['icon1'] ?? '' ?>" class="icon_img icon1" alt="">
                        <img src="<?= $reason['icon2'] ?? '' ?>" class="icon_img icon1_hover" alt="">
                    </span>
                    <div class="text text-left flex-1">
                        <h3 class="fs-16 mb10"><?= $reason['title'] ?? '' ?></h3>
                        <p class="c888 fs-13 line-h-1-7"><?= $reason['sketch'] ?? '' ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- 客户评价 -->
<section class="center clearfix" aria-labelledby="case-title">
    <div class="wow fadeInUp" data-wow-delay="0.1s">
        <div class="title-top clearfix mt45">
            <h2 id="case-title">客户评价 <span>Customer Evaluation</span></h2>
            <div class="right Customer">
                <ul id="index_case_tab">
                    <?php foreach ($caseTabs as $index => $tab): ?>
                    <li class="<?= $index === 0 ? 'active' : '' ?>"><?= $tab['name'] ?></li>
                    <?php endforeach; ?>
                    <li><a href="/cases.html">更多</a></li>
                </ul>
                <div class="border3"></div>
            </div>
        </div>
        <div class="evalu clearfix mt20 boxsh">
            <div class="evalu-1" id="index_case_tab_item">
                <?php foreach ($caseTabs as $tabIndex => $tab): ?>
                <div class="evalu-list" style="<?= $tabIndex > 0 ? 'display:none' : '' ?>">
                    <ul>
                        <?php foreach ($tab['list'] as $case): ?>
                        <li>
                            <div class="img-box">
                                <div class="img">
                                    <img class="lazy" data-original="<?= $case['image'] ?>" alt="<?= htmlspecialchars($case['title'], ENT_QUOTES, 'UTF-8') ?>服务案例" width="278" height="192">
                                </div>
                            </div>
                            <div class="text-box">
                                <p class="title-3"><a href="<?= $case['link'] ?: '/detail_cases' . $case['id'] . '.html' ?>" target="<?= $case['target'] ?? '_self' ?>"><?= $case['title'] ?></a></p>
                                <p class="infos"><?= $case['sketch'] ?? '' ?></p>
                                <p class="about-more"><a href="<?= $case['link'] ?: '/detail_cases' . $case['id'] . '.html' ?>" target="<?= $case['target'] ?? '_self' ?>" class="more-five">查看详情<span></span></a></p>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- 新闻资讯 -->
<section class="center clearfix" aria-labelledby="news-title">
    <div class="wow fadeInUp" data-wow-delay="0.1s">
        <div class="title-top clearfix mt30">
            <h2 id="news-title">新闻资讯 <span>News and Information</span></h2>
            <p class="more"><a href="/news.html">查看更多<span>›</span></a></p>
        </div>
        <div class="news-box flex flex-jcsb flex-wrap index-news-box fs-14">
            <?php foreach ($newsList as $news): ?>
            <div class="news-list pd20 boxsh flex ai-center wow fadeInUp" data-wow-delay="0.1s">
                <div class="news-left-img">
                    <a href="<?= $news['link'] ?: '/detail/news' . $news['id'] . '.html' ?>" target="<?= $news['target'] ?? '_self' ?>">
                        <img class="lazy" data-original="<?= $news['image'] ?>" alt="<?= htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8') ?>" width="230" height="162" src="<?= $news['image'] ?>">
                    </a>
                </div>
                <div class="news-right-text">
                    <h3 class="ellipsis"><a href="<?= $news['link'] ?: '/detail/news' . $news['id'] . '.html' ?>" target="<?= $news['target'] ?? '_self' ?>"><?= $news['title'] ?></a></h3>
                    <p class="mt15 c888 line-h-1-7 wline3"><?= $news['sketch'] ?? '' ?></p>
                    <p class="over-hidden mt20">
                        <span class="c888 fl">更新时间：<?= !empty($news['create_time']) && ctype_digit((string) $news['create_time']) ? date('Y-m-d H:i:s', (int) $news['create_time']) : ($news['create_time'] ?? '') ?></span>
                        <a href="<?= $news['link'] ?: '/detail/news' . $news['id'] . '.html' ?>" target="<?= $news['target'] ?? '_self' ?>" class="danger-color fr">查看详情&gt;&gt;</a>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script src="/static/home/js/index.js"></script>

<?php include VIEW_PATH . 'layout/footer.php'; ?>

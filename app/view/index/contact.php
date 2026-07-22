<?php include VIEW_PATH . 'layout/header.php'; ?>
<link rel="stylesheet" href="/static/home/css/about.css">

<div class="page-banner">
    <img src="/upload/20240510/bacfd59f43877ced86eca6d241385b84.jpg" class="w100 block" alt="">
</div>

<div>
    <div class="about_nav_2">
        <ul class="center clearfix">
        </ul>
    </div>
</div>

<div>
    <div class="wow fadeInUp" data-wow-delay="0.1s">
        <div class="center">
            <div class="title-top clearfix mt45">
                <p>联系志远<span></span></p>
            </div>
        </div>
        <div class="about-container center mt20 border-radius fs-14 line-h-2">
            <div class="contact_main border_ef flex flex-jcsb ai-center flex-wrap radius10">
                <div class="txt map_box flex1">
                    <h4 class=" mx fadeInUp" data-wow-delay="0.1s" data-wow-duration=".8s"><?= $site['name'] ?></h4>
                    <h5 class="en mx fadeInUp" data-wow-delay="0.14s" data-wow-duration=".8s">Guangdong Zhiyuan Moving Service Co., Ltd</h5>
                    <p class=" mx fadeInUp" data-wow-delay="0.22s" data-wow-duration=".8s">
                        <i class="icon"><img src="/static/home/images/icon-telephone2.png" alt=""></i>
                        <span>免费热线：<a href="tel:<?= preg_replace('/\D+/', '', (string) $site['phone']) ?>"><?= $site['phone'] ?></a></span>
                    </p>
                    <p class=" mx fadeInUp" data-wow-delay="0.22s" data-wow-duration=".8s">
                        <i class="icon"><img src="/static/home/images/icon-telephone2.png" alt=""></i>
                        <span>公司固话：<a href="tel:<?= preg_replace('/\D+/', '', (string) $site['phone']) ?>"><?= $site['phone'] ?></a></span>
                    </p>
                    <p class=" mx fadeInUp" data-wow-delay="0.26s" data-wow-duration=".8s">
                        <i class="icon"><img src="/static/home/images/icon_phone.png" alt=""></i>
                        <span>联系手机号：<a href="tel:<?= preg_replace('/\D+/', '', (string) $site['mobile']) ?>"><?= $site['mobile'] ?></a></span>
                    </p>
                    <p class=" mx fadeInUp" data-wow-delay="0.28s" data-wow-duration=".8s">
                        <i class="icon"><img src="/static/home/images/icon_email.png" alt=""></i>
                        <span>企业邮箱：<?= $site['email'] ?></span>
                    </p>
                    <p class=" mx fadeInUp" data-wow-delay="0.28s" data-wow-duration=".8s">
                        <i class="icon"><img src="/static/home/images/icon_email.png" alt=""></i>
                        <span>传 真：</span>
                    </p>
                    <p class=" mx fadeInUp" data-wow-delay="0.3s" data-wow-duration=".8s">
                        <i class="icon"><img src="/static/home/images/icon_address34.png" alt=""></i>
                        <span>公司地址：<?= $site['address'] ?></span>
                    </p>
                </div>
                <div class="img ">
                    <?php if ($contact['image'] ?? ''): ?>
                    <img src="<?= $contact['image'] ?>" class=" block" alt="">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div>
    <div class="wow fadeInUp" data-wow-delay="0.1s">
        <div class="center">
            <div class="title-top clearfix mt45">
                <p>服务城市<span>Service City</span></p>
            </div>
        </div>
        <div class="hot-city-box pd25 bgfff clearfix center mt20">
            <div id="dot-box" class="dot-box mt40 masonry">
                <?php
                $cities = [
                    ['city' => '广州市', 'title' => '广州志远搬家服务有限公司', 'addr' => $site['address'] . "\n业务一线：" . $site['phone'] . "\n业务二线：" . ($site['mobile'] ?? '')],
                ];
                foreach ($cities as $city):
                ?>
                <div class="dot-list masonry-brick" data-city="<?= $city['city'] ?>" data-title="<?= $city['title'] ?>">
                    <h3><?= $city['title'] ?></h3>
                    <div class="text-box"><?= $city['addr'] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include VIEW_PATH . 'layout/footer.php'; ?>

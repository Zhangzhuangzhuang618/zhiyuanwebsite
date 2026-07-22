</main>
<!-- 数据统计模块 -->
<div>
    <div class="number" style="background-image: url(/upload/20250115/311220780e4cadb5939576640896fa23.png);">
        <div class="center">
            <div class="left wow fadeInLeft" data-wow-delay="0.1s">
                <img class="lazy" data-original="/upload/20260601/8b197a2e3bc602e1b72c263b6ff60a1e.png" alt="志远搬家服务数据">
            </div>
            <div class="right wow fadeInRight" data-wow-delay="0.1s">
                <ul id="number-list">
                    <li><p><span class="count-up">20</span>年</p><p><i></i>服务经验</p></li>
                    <li><p><span class="count-up">500</span>万</p><p><i></i>服务客户群体</p></li>
                    <li><p><span class="count-up">2000</span>位</p><p><i></i>专业搬家技师</p></li>
                    <li><p><span class="count-up">100</span>位</p><p><i></i>专属客服服务</p></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- 页脚 -->
<footer>
    <div class="n-footer wow fadeInUp">
        <div class="n-footer-top">
            <div class="clearfix center">
                <ul class="n-slide-nav">
                    <li class="active">热门城市</li>
                    <li>服务项目</li>
                </ul>
            </div>
            <div class="n-footer-con clearfix center">
                <div class="footer-slide">
                    <?php foreach ($city_list as $city): ?>
                    <a href="http://<?= $city['en_mark'] ?>.zhiyuanbj.cn"><?= $city['mark'] ?>搬家</a>
                    <?php endforeach; ?>
                </div>
                <div class="footer-slide">
                    <?php foreach ($footer_services as $service): ?>
                    <a href="<?= $service['link'] ?>" target="<?= $service['target'] ?? '_self' ?>"><?= $service['title'] ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="n-footer-bottom center clearfix">
            <div class="n-footer-left">
                <ul class="n-footer-nav clearfix">
                    <?php foreach ($foot_nav as $item): ?>
                    <li><a href="<?= $item['href'] ?: '/' . $item['url_model'] . '/' . $item['id'] . '.html' ?>"><?= $item['title'] ?></a></li>
                    <?php endforeach; ?>
                    <li><a href="/faq.html">搬家常见问题</a></li>
                </ul>
                <p class="beian">友情链接：<a href="https://zrbanjia.com" target="_blank" rel="noopener noreferrer">众人搬家</a></p>
                <p class="beian">Copyright &copy; 2025 <?= $site['name'] ?> 版权所有
                    <a target="_blank" href="http://beian.miit.gov.cn/"><?= $site['icp'] ?></a>
                </p>
            </div>
            <div class="n-footer-right">
                <div class="n-ewm">
                    <div><img class="lazy" data-original="<?= $site['wechat_code'] ?>" alt="" width="100%"><p>微信咨询</p></div>
                    <div><img class="lazy" data-original="<?= $site['wechat_code2'] ?: $site['wechat_code'] ?>" alt="" width="100%"><p>公众号</p></div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- 右侧悬浮 -->
<div class="rightFix">
    <ul>
        <li>
            <div class="img-box"></div><p>热线电话</p>
            <div class="text-box">
                <p><i>全国服务热线</i><span class="time">08:00 - 24:00</span><span class="tel"><a href="tel:<?= preg_replace('/\D+/', '', (string) $site['phone']) ?>"><?= $site['phone'] ?></a></span></p>
                <p><i>售后服务热线</i><span class="time">08:00 - 22:00</span><span class="tel"><a href="tel:<?= preg_replace('/\D+/', '', (string) $site['phone']) ?>"><?= $site['phone'] ?></a></span></p>
            </div>
        </li>
        <li><a href="tel:02085627757"><div class="img-box"></div><p>在线咨询</p></a></li>
        <li class="wxkefu">
            <div class="img-box"></div><p>微信咨询</p>
            <div class="text-box">
                <p>扫一扫在线交谈</p>
                <p><img src="<?= $site['wechat_code'] ?>" alt="" width="100%"></p>
            </div>
        </li>
        <li class="getBaojia"><div class="img-box"></div><p>价格咨询</p></li>
    </ul>
    <div class="top"><span></span></div>
</div>

<!-- 底部固定导航（移动端） -->
<div class="app_foot_box"></div>
<div class="fixedBot">
    <a href="/index.html" class="fixed-link"><img src="/static/home/images/icon_home.png" alt=""><div class="fixed-title">官方首页</div></a>
    <a href="/products.html" class="fixed-link"><img src="/static/home/images/icon_product.png" alt=""><div class="fixed-title">产品中心</div></a>
    <a href="tel:<?= preg_replace('/\D+/', '', (string) $site['mobile']) ?>" class="fixed-link"><img src="/static/home/images/icon_tel2.png" alt=""><div class="fixed-title">电话咨询</div></a>
    <a href="javascript:;" class="fixed-link" onclick="goTop()"><img src="/static/home/images/icon_gotop2.png" alt=""><div class="fixed-title">返回顶部</div></a>
</div>

<!-- 报价弹窗 -->
<div class="popup-box">
    <div id="Popup">
        <button type="button" class="close-popup" aria-label="关闭报价弹窗"></button>
        <div class="container">
            <div class="box1">
                <div class="left-box">
                    <h1><span>搬家服务</span>价格估算</h1>
                    <p>选择服务类型与预估体积，立即查看参考报价</p>
                    <div class="inside-box quote-calculator">
                        <div class="quote-layout">
                            <div class="quote-fields">
                            <div class="quote-tabs" role="tablist" aria-label="搬家服务类型">
                                <button type="button" class="is-active" data-quote-type="half" role="tab" aria-selected="true">半日式搬家</button>
                                <button type="button" data-quote-type="japanese" role="tab" aria-selected="false">日式精品搬家</button>
                            </div>
                            <div class="quote-grid">
                                <label class="quote-field">出发地
                                    <select id="quote-from" aria-label="出发地">
                                        <option value="天河区">天河区</option><option value="越秀区">越秀区</option><option value="海珠区">海珠区</option><option value="白云区">白云区</option><option value="番禺区">番禺区</option><option value="黄埔区">黄埔区</option><option value="广州市外">广州市外</option>
                                    </select>
                                </label>
                                <label class="quote-field">目的地
                                    <select id="quote-to" aria-label="目的地">
                                        <option value="天河区">天河区</option><option value="越秀区">越秀区</option><option value="海珠区">海珠区</option><option value="白云区">白云区</option><option value="番禺区">番禺区</option><option value="黄埔区">黄埔区</option><option value="广州市外">广州市外</option>
                                    </select>
                                </label>
                                <label class="quote-field">车型
                                    <select id="quote-vehicle" aria-label="车型">
                                        <option value="面包车" data-included-km="30">面包车（含 30 公里）</option><option value="厢式货车" data-included-km="30">厢式货车（含 30 公里）</option><option value="4.2米厢式货车" data-included-km="50">4.2 米厢式货车（含 50 公里）</option>
                                    </select>
                                </label>
                                <label class="quote-field">导航距离（公里）
                                    <input type="number" id="quote-distance" value="30" min="0" max="999" step="0.5" inputmode="decimal" aria-label="导航距离">
                                </label>
                            </div>
                            <label class="quote-field quote-volume">预估物品体积（立方米）
                                <input type="number" id="quote-volume" value="5" min="0" max="999" step="0.5" inputmode="decimal" aria-label="预估物品体积">
                            </label>
                            <fieldset class="quote-items"><legend>大件物品（可多选）</legend>
                                <label><input type="checkbox" class="quote-item" data-price="150">双开门冰箱</label><label><input type="checkbox" class="quote-item" data-price="200">嵌入式冰箱</label><label><input type="checkbox" class="quote-item" data-price="150">壁挂电视</label><label><input type="checkbox" class="quote-item" data-price="350">立式钢琴</label><label><input type="checkbox" class="quote-item" data-price="80">跑步机</label>
                            </fieldset>
                            </div>
                            <aside class="quote-summary" aria-label="报价结果与服务说明">
                                <div class="quote-result" aria-live="polite"><span id="quote-price-label">半日式搬家预估价</span><strong>¥<b id="quote-price">1400</b> 起</strong><em id="quote-breakdown">半日式 · 天河区→天河区 · 面包车 · 按 5 立方最低起算</em></div>
                                <div class="quote-service-note"><b id="quote-service-name">半日式搬家服务</b><p id="quote-service-description">包含全程打包，无需亲自动手。</p></div>
                                <a class="pianoBtn" href="tel:02085627757">一键拨号，获取准确报价</a>
                                <p class="warning" id="quote-warning">*半日式搬家 280 元/立方，最低按 5 立方起算；最终以客服确认的作业方案为准</p>
                            </aside>
                        </div>
                        <section class="quote-hot-cities" aria-labelledby="quote-hot-cities-title">
                            <h2 id="quote-hot-cities-title">热门服务城市</h2>
                            <div class="quote-city-list"><span>广州市</span><span>深圳市</span><span>珠海市</span><span>汕头市</span><span>佛山市</span><span>韶关市</span><span>湛江市</span><span>肇庆市</span><span>江门市</span><span>茂名市</span><span>惠州市</span><span>梅州市</span><span>汕尾市</span><span>河源市</span><span>阳江市</span><span>清远市</span><span>东莞市</span><span>中山市</span><span>潮州市</span><span>揭阳市</span><span>云浮市</span></div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <div class="ajax-msg"><p class="content-msg"></p><div class="ewm"><img src="<?= $site['wechat_code'] ?>" alt="" width="100%"></div></div>
    </div>
</div>

<script>
function goTop(){ $("html,body").stop().animate({scrollTop: 0}, 1000); }
function updateQuoteCalculator(){
    var from = $('#quote-from').val();
    var to = $('#quote-to').val();
    var quoteType = $('.quote-tabs button.is-active').data('quote-type');
    var isJapanese = quoteType === 'japanese';
    var unitPrice = isJapanese ? 320 : 280;
    var minimumVolume = isJapanese ? 10 : 5;
    var volume = Math.max(0, Number($('#quote-volume').val()) || 0);
    var billedVolume = Math.max(minimumVolume, volume);
    var vehicle = $('#quote-vehicle option:selected');
    var includedKm = Number(vehicle.data('included-km'));
    var distance = Math.max(0, Number($('#quote-distance').val()) || 0);
    var mileageFee = Math.max(0, distance - includedKm) * 7;
    var itemFee = 0;
    $('.quote-item:checked').each(function(){ itemFee += Number($(this).data('price')); });
    var price = billedVolume * unitPrice + mileageFee + itemFee;
    var serviceName = isJapanese ? '日式精品搬家' : '半日式搬家';
    var extras = [];
    if (mileageFee) extras.push('超距¥' + mileageFee);
    if (itemFee) extras.push('大件¥' + itemFee);
    $('#quote-price').text(price);
    $('#quote-price-label').text(serviceName + '预估价');
    $('#quote-breakdown').text(serviceName + ' · ' + from + '→' + to + ' · ' + vehicle.val() + '（含 ' + includedKm + ' 公里）· 按 ' + billedVolume + ' 立方' + (volume < minimumVolume ? '最低起算' : '计费') + (extras.length ? ' · ' + extras.join(' · ') : ''));
    $('#quote-service-name').text(serviceName + '服务');
    $('#quote-service-description').text(isJapanese ? '包含全程打包及搬入后的物品复原，无需亲自动手。' : '包含全程打包，无需亲自动手。');
    $('#quote-warning').text('*' + serviceName + ' ' + unitPrice + ' 元/立方，最低按 ' + minimumVolume + ' 立方起算；超出车型免费里程按 7 元/公里，大件按所选项目计费，最终以客服确认的作业方案为准');
}
</script>
<script>
$(function(){
    $('#quote-from, #quote-to, #quote-vehicle').on('change', updateQuoteCalculator);
    $('#quote-volume, #quote-distance').on('input', updateQuoteCalculator);
    $('.quote-item').on('change', updateQuoteCalculator);
    $('.quote-tabs button').on('click', function(){
        $('.quote-tabs button').removeClass('is-active').attr('aria-selected', 'false');
        $(this).addClass('is-active').attr('aria-selected', 'true');
        updateQuoteCalculator();
    });
    updateQuoteCalculator();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var phonePattern = /(^|[^\d])(1[3-9]\d{9})(?!\d)/;
    var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
        acceptNode: function (node) {
            var parent = node.parentElement;
            if (!parent || parent.closest('a, script, style, textarea, option, button')) {
                return NodeFilter.FILTER_REJECT;
            }
            return phonePattern.test(node.nodeValue) ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
        }
    });
    var nodes = [];
    while (walker.nextNode()) nodes.push(walker.currentNode);

    nodes.forEach(function (node) {
        var parts = node.nodeValue.split(/(^|[^\d])(1[3-9]\d{9})(?!\d)/);
        var fragment = document.createDocumentFragment();
        parts.forEach(function (part) {
            if (/^1[3-9]\d{9}$/.test(part)) {
                var link = document.createElement('a');
                link.href = 'tel:' + part;
                link.className = 'phone-link';
                link.textContent = part;
                fragment.appendChild(link);
            } else if (part) {
                fragment.appendChild(document.createTextNode(part));
            }
        });
        node.parentNode.replaceChild(fragment, node);
    });
});
</script>
<script src="/static/home/js/swiper.min.js"></script>
<script src="/static/home/js/s.js"></script>
<script src="/static/home/js/jquery.lazyload.js"></script>
<script src="/static/home/js/wow.js"></script>
<script src="/static/home/js/main.js"></script>
<script src="/static/home/js/header.js"></script>
<script src="/static/home/js/layer.js"></script>
<script src="/static/home/js/mediaelement-and-player.min.js"></script>
<script src="/static/home/js/acc_ch.js"></script>
</body>
</html>

/* *
 * 全局空间 Gcity
 * */
var Gcity = {};
/* *
 * 静态方法集
 * @name _m
 * */
Gcity._m = {
    /* 选择元素 */
    $:function (arg, context) {
        var tagAll, n, eles = [], i, sub = arg.substring(1);
        context = context || document;
        if (typeof arg == 'string') {
            switch (arg.charAt(0)) {
                case '#':
                    return document.getElementById(sub);
                    break;
                case '.':
                    if (context.getElementsByClassName) return context.getElementsByClassName(sub);
                    tagAll = Gcity._m.$('*', context);
                    n = tagAll.length;
                    for (i = 0; i < n; i++) {
                        if (tagAll[i].className.indexOf(sub) > -1) eles.push(tagAll[i]);
                    }
                    return eles;
                    break;
                default:
                    return context.getElementsByTagName(arg);
                    break;
            }
        }
    },

    /* 绑定事件 */
    on:function (node, type, handler) {
        node.addEventListener ? node.addEventListener(type, handler, false) : node.attachEvent('on' + type, handler);
    },

    /* 获取事件 */
    getEvent:function(event){
        return event || window.event;
    },

    /* 获取事件目标 */
    getTarget:function(event){
        return event.target || event.srcElement;
    },

    /* 获取元素位置 */
    getPos:function (node) {
        var scrollx = document.documentElement.scrollLeft || document.body.scrollLeft,
                scrollt = document.documentElement.scrollTop || document.body.scrollTop;
        var pos = node.getBoundingClientRect();
        return {top:pos.top + scrollt, right:pos.right + scrollx, bottom:pos.bottom + scrollt, left:pos.left + scrollx }
    },

    /* 添加样式名 */
    addClass:function (c, node) {
        if(!node)return;
        node.className = Gcity._m.hasClass(c,node) ? node.className : node.className + ' ' + c ;
    },

    /* 移除样式名 */
    removeClass:function (c, node) {
        var reg = new RegExp("(^|\\s+)" + c + "(\\s+|$)", "g");
        if(!Gcity._m.hasClass(c,node))return;
        node.className = reg.test(node.className) ? node.className.replace(reg, '') : node.className;
    },

    /* 是否含有CLASS */
    hasClass:function (c, node) {
        if(!node || !node.className)return false;
        return node.className.indexOf(c)>-1;
    },

    /* 阻止冒泡 */
    stopPropagation:function (event) {
        event = event || window.event;
        event.stopPropagation ? event.stopPropagation() : event.cancelBubble = true;
    },
    /* 去除两端空格 */
    trim:function (str) {
        return str.replace(/^\s+|\s+$/g,'');
    }
};

/* 所有城市数据,可以按照格式自行添加（北京|beijing|bj），前16条为热门城市 */

Gcity.allCity = ['加拿大|CANADA|jnd','新加坡|SINGAPORE|xjp','马来西亚|MALAYSIA|mlxy','荷兰|NETHERLANDS|hl','新西兰|NEWZEALAND|xxl','澳大利亚|AUSTRALIA|adly','法国|FRANCE|fg','美国|USA|mg','墨西哥|Mexico|mxg','巴西|Brazil|bx','日本|JAPAN|rb','德国|GERMANY|dg','瑞典|SWEDEN|rd','瑞士|SWITZERLAND|rs','英国|England|yg','意大利|ITALY|ydl','爱尔兰|IRELAND|ael','西班牙|SPAIN|xby','斯里兰卡|SRILANKA|sllk','苏丹|SUDAN|sd','阿尔及利亚|ALGERIA|aejly','安哥拉|Angola|agl','阿根廷|Argentina|agt','阿鲁巴|Aruba|alb','奥地利|AUSTRIA|adl','巴哈马群岛之首都|Bahamas|bhmqdzsd','孟加拉国|BANGLADESH|mjlg','巴巴多斯|Barbados|bbds','比利时|BELGIUM|bls','伯利兹|BELIZE|blz','玻利维亚|BOLIVIA|blwy','博茨瓦纳|Botswana|bcwn','汶莱|BRUNEI|wl','保加利亚|BULGARIA|bjly','缅甸|BURMA|md','柬埔寨|CAMBODIA|jpz','智利|Chile|zl','哥伦比亚|Colombia|glby','哥斯达黎加|CostaRica|gsdlj','克罗地亚|CROATIA|kldy','古巴|Cuba|gb','库拉索|Curacao|kls','塞浦路斯|CYPRUS|spls','捷克|CZECH|jk','丹麦|DENMARK|dm','多米尼加共和国|Dominica|dmnjghg','厄瓜多尔|Ecuador|egde','埃及|EGYPT|aj','萨尔瓦多|ElSalvador|sewd','爱沙尼亚|ESTONIA|asny','斐济群岛|FIJIISLANDS|fjqd','芬兰|FINLAND|fl','加纳|Ghana|jn','希腊|GREECE|xl','格林纳达|Grenada|glnd','瓜德罗普岛|Guadeloupe|gdlpd','危地马拉|Guatemala|wdml','圭亚那|Guyana|gyn','海地|Haiti|hd','洪都拉斯|Honduras|hdls','中国香港|HONGKONG|zgxg','匈牙利|HUNGARY|xyl','印度|INDIA|yd','印度尼西亚|INDONESIA|ydnxy','伊朗|IRAN|yl','以色列|ISRAEL|ysl','牙买加|Jamaica|ymj','约旦|JORDAN|yd','肯尼亚|KENYA|kny','韩国|KOREA|hg','老挝|Laos|lw','拉脱维亚|LATVIJAS|ltwy','黎巴嫩|LEBANON|lbn','莱索托|Lesotho|lst','立陶宛|LITHUANIA|ltw','莫埃利岛|Longoni|mald','马达加斯加岛|Madagascar|mdjsjd','马拉维|Malawi|mlw','马尔代夫岛|MALDIVEISLAND|medfd','马耳他|MALTA|met','马提尼克岛|Martinique|mtnkd','毛里求斯|MAURITIUSIS|mlqs','摩洛哥|MOROCCO|mlg','莫桑比克|Mozambique|msbk','尼加拉瓜|Nicaragua|njlg','尼日利亚|Nigeria|nrly','挪威|NORWAY|nw','阿曼|OMAN|am','巴基斯坦|PAKISTAN|bjst','巴拿马|Panama|bnm','巴拉圭|Paraguay|blg','波斯湾|PERSIANGULF|bsw','秘鲁|Peru|ml','菲利普斯堡|Philipburg|PHILIPBURG','菲律宾|PHILIPPINES|flb','波兰|POLAND|bl','葡萄牙|PORTUGAL|pty','波多黎各|PuertoRico|bdlg','卡塔尔|QATAR|kte','马拉维共和国|RepublicofMalawi|mlwghg','法属留尼旺|Reunion|fslnw','罗马尼亚|ROMANIA|lmny','俄罗斯|RUSSIA|els','卢旺达共和国|Rwanda|lwdghg','圣约翰|SaintJohn|syh','圣基茨和尼维斯|SaintKittsNevis|sjchnws','圣卢西亚首都|SaintLucia|slxysd','拉丁美洲岛国圣文森特和格林纳丁斯首都|SaintVincentandtheGrenadines|ldmzdgswsthglndssd','城主托马斯|SantoThomas|cztms','沙特阿拉伯|SAUDIARABIA|stalb','斯洛伐克|Slovakia|slfk','斯诺文尼亚|SLOVENIA|snwny','西印度群岛小岛屿|SmallislandsoftheWestIndies|xydqdxdy','南非|SOUTHAFRICA|nf','苏里南|Suriname|sln','斯威士兰|Swaziland|swsl','叙利亚|SYRIA|xly','中国台湾|TAIWAN|zgtw','坦桑尼亚|TANZANIA|tsny','泰国|THAILAND|tg','印度次大陆|TheIndiansubcontinent|ydcdl','阿拉伯联合酋长国|TheUnitedArabEmirates|alblhqcg','特立尼达和多巴哥|Trinidad|tlndhdbg','突尼斯|TUNISIA|tns','土耳其|TURKEY|teq','阿联酋|UAE|alq','乌干达|Uganda|wgd','乌克兰|Ukraine|wkl','乌拉圭|Uruguay|wlg','委内瑞拉|Venezuela|wnrl','越南|VIETNAM|yn','维尔京群岛|VirginIslands|wejqd','维尔京群岛|VirginIslands|wejqd','也门|YEMEN|ym','赞比亚|Zambia|zby','津巴布韦|RepublicofZimbabwe|jbbw'];

/* 正则表达式 筛选中文城市名、拼音、首字母 */

Gcity.regEx = /^([\u4E00-\u9FA5\uf900-\ufa2d]+)\|(\w+)\|(\w)\w*$/i;
Gcity.regExChiese = /([\u4E00-\u9FA5\uf900-\ufa2d]+)/;

/* *
 * 格式化城市数组为对象oCity，按照a-h,i-p,q-z,hot热门城市分组：
 * {HOT:{hot:[]},ABCDEFGH:{a:[1,2,3],b:[1,2,3]},IJKLMNOP:{i:[1.2.3],j:[1,2,3]},QRSTUVWXYZ:{}}
 * */
(function () {
    var citys = Gcity.allCity, match, letter,
            regEx = Gcity.regEx,
            reg2 = /^[a-b]$/i, reg3 = /^[c-d]$/i, reg4 = /^[e-g]$/i,reg5 = /^[h]$/i,reg6 = /^[j]$/i,reg7 = /^[k-l]$/i,reg8 =  /^[m-p]$/i,reg9 =  /^[q-r]$/i,reg10 =  /^[s]$/i,reg11 =  /^[t]$/i,reg12 =  /^[w]$/i,reg13 =  /^[x]$/i,reg14 =  /^[y]$/i,reg15 =  /^[z]$/i;
    if (!Gcity.oCity) {
        Gcity.oCity = {hot:{},AB:{},CD:{},EFG:{},H:{},J:{},KL:{},MNP:{},QR:{},S:{},T:{},W:{},X:{},Y:{},Z:{}};
        //console.log(citys.length);
        for (var i = 0, n = citys.length; i < n; i++) {
            match = regEx.exec(citys[i]);
            letter = match[3].toUpperCase();
            if (reg2.test(letter)) {
                if (!Gcity.oCity.AB[letter]) Gcity.oCity.AB[letter] = [];
                Gcity.oCity.AB[letter].push(match[1]);
            } else if (reg3.test(letter)) {
                if (!Gcity.oCity.CD[letter]) Gcity.oCity.CD[letter] = [];
                Gcity.oCity.CD[letter].push(match[1]);
            } else if (reg4.test(letter)) {
                if (!Gcity.oCity.EFG[letter]) Gcity.oCity.EFG[letter] = [];
                Gcity.oCity.EFG[letter].push(match[1]);
            }else if (reg5.test(letter)) {
                if (!Gcity.oCity.H[letter]) Gcity.oCity.H[letter] = [];
                Gcity.oCity.H[letter].push(match[1]);
            }else if (reg6.test(letter)) {
                if (!Gcity.oCity.J[letter]) Gcity.oCity.J[letter] = [];
                Gcity.oCity.J[letter].push(match[1]);
            }else if (reg7.test(letter)) {
                if (!Gcity.oCity.KL[letter]) Gcity.oCity.KL[letter] = [];
                Gcity.oCity.KL[letter].push(match[1]);
            }else if (reg8.test(letter)) {
                if (!Gcity.oCity.MNP[letter]) Gcity.oCity.MNP[letter] = [];
                Gcity.oCity.MNP[letter].push(match[1]);
            }else if (reg9.test(letter)) {
                if (!Gcity.oCity.QR[letter]) Gcity.oCity.QR[letter] = [];
                Gcity.oCity.QR[letter].push(match[1]);
            }else if (reg10.test(letter)) {
                if (!Gcity.oCity.S[letter]) Gcity.oCity.S[letter] = [];
                Gcity.oCity.S[letter].push(match[1]);
            }else if (reg11.test(letter)) {
                if (!Gcity.oCity.T[letter]) Gcity.oCity.T[letter] = [];
                Gcity.oCity.T[letter].push(match[1]);
            }else if (reg12.test(letter)) {
                if (!Gcity.oCity.W[letter]) Gcity.oCity.W[letter] = [];
                Gcity.oCity.W[letter].push(match[1]);
            }else if (reg13.test(letter)) {
                if (!Gcity.oCity.X[letter]) Gcity.oCity.X[letter] = [];
                Gcity.oCity.X[letter].push(match[1]);
            }else if (reg14.test(letter)) {
                if (!Gcity.oCity.Y[letter]) Gcity.oCity.Y[letter] = [];
                Gcity.oCity.Y[letter].push(match[1]);
            }else if (reg15.test(letter)) {
                if (!Gcity.oCity.Z[letter]) Gcity.oCity.Z[letter] = [];
                Gcity.oCity.Z[letter].push(match[1]);
            }

            /* 热门城市 前16条 */
            if(i<20){
                if(!Gcity.oCity.hot['hot']) Gcity.oCity.hot['hot'] = [];
                Gcity.oCity.hot['hot'].push(match[1]);
            }
        }
    }
})();


/* 城市HTML模板 */
Gcity._template = [
    '<p class="tip">直接输入可搜索城市(支持汉字/拼音)</p>',
    '<ul>',
    '<li class="on">热门城市</li>',
    '<li>AB</li>',
    '<li>CD</li>',
    '<li>EFG</li>',
    '<li>H</li>',
    '<li>J</li>',
    '<li>KL</li>',
    '<li>MNP</li>',
    '<li>QR</li>',
    '<li>S</li>',
    '<li>T</li>',
    '<li>W</li>',
    '<li>X</li>',
    '<li>Y</li>',
    '<li>Z</li>',
    '</ul>'
];

/* *
 * 城市控件构造函数
 * @CitySelector
 * */

Gcity.CitySelector = function () {
    this.initialize.apply(this, arguments);
};

Gcity.CitySelector.prototype = {

    constructor:Gcity.CitySelector,

    /* 初始化 */

    initialize :function (options) {
        var input = options.input;
        this.input = Gcity._m.$('#'+ input);
        this.inputEvent();
    },

    /* *
        

    /* *
     * @createWarp
     * 创建城市BOX HTML 框架
     * */

    createWarp:function(){
        var inputPos = Gcity._m.getPos(this.input);
        var div = this.rootDiv = document.createElement('div');
        var that = this;

        // 设置DIV阻止冒泡
        Gcity._m.on(this.rootDiv,'click',function(event){
            Gcity._m.stopPropagation(event);
        });

        // 设置点击文档隐藏弹出的城市选择框
        Gcity._m.on(document, 'click', function (event) {
            event = Gcity._m.getEvent(event);
            var target = Gcity._m.getTarget(event);
            if(target == that.input) return false;
            //console.log(target.className);
            if (that.cityBox)Gcity._m.addClass('hide', that.cityBox);
            if (that.ul)Gcity._m.addClass('hide', that.ul);
            if(that.myIframe)Gcity._m.addClass('hide',that.myIframe);
        });
        div.className = 'citySelector';
        div.style.position = 'absolute';
        div.style.left = inputPos.left + 'px';
        div.style.top = inputPos.bottom + 5 + 'px';
        div.style.zIndex = 999999;

        // 判断是否IE6，如果是IE6需要添加iframe才能遮住SELECT框
        var isIe = (document.all) ? true : false;
        var isIE6 = this.isIE6 = isIe && !window.XMLHttpRequest;
        if(isIE6){
            var myIframe = this.myIframe =  document.createElement('iframe');
            myIframe.frameborder = '0';
            myIframe.src = 'about:blank';
            myIframe.style.position = 'absolute';
            myIframe.style.zIndex = '-1';
            this.rootDiv.appendChild(this.myIframe);
        }

        var childdiv = this.cityBox = document.createElement('div');
        childdiv.className = 'cityBox';
        childdiv.id = 'cityBox';
        childdiv.innerHTML = Gcity._template.join('');
        var hotCity = this.hotCity =  document.createElement('div');
        hotCity.className = 'hotCity';
        childdiv.appendChild(hotCity);
        div.appendChild(childdiv);
        this.createHotCity();
        this.container = div;
    },

    /* *
     * @createHotCity
     * TAB下面DIV：hot,a-h,i-p,q-z 分类HTML生成，DOM操作
     * {HOT:{hot:[]},ABCDEFGH:{a:[1,2,3],b:[1,2,3]},IJKLMNOP:{},QRSTUVWXYZ:{}}
     **/

    createHotCity:function(){
        var odiv,odl,odt,odd,odda=[],str,key,ckey,sortKey,regEx = Gcity.regEx,
                oCity = Gcity.oCity;
        for(key in oCity){
            odiv = this[key] = document.createElement('div');
            // 先设置全部隐藏hide
            odiv.className = key + ' ' + 'cityTab hide';
            sortKey=[];
            for(ckey in oCity[key]){
                sortKey.push(ckey);
                // ckey按照ABCDEDG顺序排序
                sortKey.sort();
            }
            for(var j=0,k = sortKey.length;j<k;j++){
                odl = document.createElement('dl');
                odt = document.createElement('dt');
                odd = document.createElement('dd');
                odt.innerHTML = sortKey[j] == 'hot'?'&nbsp;':sortKey[j];
                odda = [];
                for(var i=0,n=oCity[key][sortKey[j]].length;i<n;i++){
                    str = '<a>' + oCity[key][sortKey[j]][i] + '</a>';
                    odda.push(str);
                }
                odd.innerHTML = odda.join('');
                odl.appendChild(odt);
                odl.appendChild(odd);
                odiv.appendChild(odl);
            }

            // 移除热门城市的隐藏CSS
            Gcity._m.removeClass('hide',this.hot);
            this.hotCity.appendChild(odiv);
        }
        document.body.appendChild(this.rootDiv);
        /* IE6 */
        this.changeIframe();

        this.tabChange();
        this.linkEvent();
    },

    /* *
     *  tab按字母顺序切换
     *  @ tabChange
     * */

    tabChange:function(){
        var lis = Gcity._m.$('li',this.cityBox);
        var divs = Gcity._m.$('div',this.hotCity);
        var that = this;
        for(var i=0,n=lis.length;i<n;i++){
            lis[i].index = i;
            lis[i].onclick = function(){
                for(var j=0;j<n;j++){
                    Gcity._m.removeClass('on',lis[j]);
                    Gcity._m.addClass('hide',divs[j]);
                }
                Gcity._m.addClass('on',this);
                Gcity._m.removeClass('hide',divs[this.index]);
                /* IE6 改变TAB的时候 改变Iframe 大小*/
                that.changeIframe();
            };
        }
    },

    /* *
     * 城市LINK事件
     *  @linkEvent
     * */

    linkEvent:function(){
        var links = Gcity._m.$('a',this.hotCity);
        var that = this;
        for(var i=0,n=links.length;i<n;i++){
            links[i].onclick = function(){
                that.input.value = this.innerHTML;
                Gcity._m.addClass('hide',that.cityBox);
                /* 点击城市名的时候隐藏myIframe */
                Gcity._m.addClass('hide',that.myIframe);
            }
        }
    },

    /* *
     * INPUT城市输入框事件
     * @inputEvent
     * */

    inputEvent:function(){
        var that = this;
        Gcity._m.on(this.input,'click',function(event){
            event = event || window.event;
            if(!that.cityBox){
                that.createWarp();
            }else if(!!that.cityBox && Gcity._m.hasClass('hide',that.cityBox)){
                // slideul 不存在或者 slideul存在但是是隐藏的时候 两者不能共存
                if(!that.ul || (that.ul && Gcity._m.hasClass('hide',that.ul))){
                    Gcity._m.removeClass('hide',that.cityBox);

                    /* IE6 移除iframe 的hide 样式 */
                    //alert('click');
                    Gcity._m.removeClass('hide',that.myIframe);
                    var inputPos = Vcity._m.getPos(that.input);
                    that.container.style.left = inputPos.left + 'px';
                    that.container.style.top = inputPos.bottom + 5 + 'px';
                    that.changeIframe();
                }
            }
        });
        // Gcity._m.on(this.input,'focus',function(){
        //     that.input.select();
        //     if(that.input.value == '城市名') that.input.value = '';
        // });
        Gcity._m.on(this.input,'blur',function(){
            // if(that.input.value == '') that.input.value = '城市名';
            
            var value = Gcity._m.trim(that.input.value);
            if(value != ''){
                var reg = new RegExp("^" + value + "|\\|" + value, 'gi');
                var flag=0;
                for (var i = 0, n = Gcity.allCity.length; i < n; i++) {
                    if (reg.test(Gcity.allCity[i])) {
                        flag++;
                    }
                }
                if(flag==0){
                    that.input.value= '';
                }else{
                    var lis = Gcity._m.$('li',that.ul);
                    if(typeof lis == 'object' && lis['length'] > 0){
                        var li = lis[0];
                        var bs = li.children;
                        if(bs && bs['length'] > 1){
                            that.input.value = bs[0].innerHTML;
                        }
                    }else{
                        that.input.value = '';
                    }
                }
            }

        });
        Gcity._m.on(this.input,'keyup',function(event){
            event = event || window.event;
            var keycode = event.keyCode;
            Gcity._m.addClass('hide',that.cityBox);
            that.createUl();

            /* 移除iframe 的hide 样式 */
            Gcity._m.removeClass('hide',that.myIframe);

            // 下拉菜单显示的时候捕捉按键事件
            if(that.ul && !Gcity._m.hasClass('hide',that.ul) && !that.isEmpty){
                that.KeyboardEvent(event,keycode);
            }
        });
    },

    /* *
     * 生成下拉选择列表
     * @ createUl
     * */

    createUl:function () {
        //console.log('createUL');
        var str;
        var value = Gcity._m.trim(this.input.value);
        // 当value不等于空的时候执行
        if (value !== '') {
            var reg = new RegExp("^" + value + "|\\|" + value, 'gi');
            // 此处需设置中文输入法也可用onpropertychange
            var searchResult = [];
            for (var i = 0, n = Gcity.allCity.length; i < n; i++) {
                if (reg.test(Gcity.allCity[i])) {
                    var match = Gcity.regEx.exec(Gcity.allCity[i]);
                    if (searchResult.length !== 0) {
                        str = '<li><b class="cityname">' + match[1] + '</b><b class="cityspell">' + match[2] + '</b></li>';
                    } else {
                        str = '<li class="on"><b class="cityname">' + match[1] + '</b><b class="cityspell">' + match[2] + '</b></li>';
                    }
                    searchResult.push(str);
                }
            }
            this.isEmpty = false;
            // 如果搜索数据为空
            if (searchResult.length == 0) {
                this.isEmpty = true;
                str = '<li class="empty">对不起，没有找到 "<em>' + value + '</em>"</li>';
                searchResult.push(str);
            }
            // 如果slideul不存在则添加ul
            if (!this.ul) {
                var ul = this.ul = document.createElement('ul');
                ul.className = 'cityslide mCustomScrollbar';
                this.rootDiv && this.rootDiv.appendChild(ul);
                // 记录按键次数，方向键
                this.count = 0;
            } else if (this.ul && Gcity._m.hasClass('hide', this.ul)) {
                this.count = 0;
                Gcity._m.removeClass('hide', this.ul);
            }
            this.ul.innerHTML = searchResult.join('');

            /* IE6 */
            this.changeIframe();

            // 绑定Li事件
            this.liEvent();
        }else{
            Gcity._m.addClass('hide',this.ul);
            Gcity._m.removeClass('hide',this.cityBox);

            Gcity._m.removeClass('hide',this.myIframe);

            this.changeIframe();
        }
    },

    /* IE6的改变遮罩SELECT 的 IFRAME尺寸大小 */
    changeIframe:function(){
        if(!this.isIE6)return;
        this.myIframe.style.width = this.rootDiv.offsetWidth + 'px';
        this.myIframe.style.height = this.rootDiv.offsetHeight + 'px';
    },

    /* *
     * 特定键盘事件，上、下、Enter键
     * @ KeyboardEvent
     * */

    KeyboardEvent:function(event,keycode){
        var lis = Gcity._m.$('li',this.ul);
        var len = lis.length;
        switch(keycode){
            case 40: //向下箭头↓
                this.count++;
                if(this.count > len-1) this.count = 0;
                for(var i=0;i<len;i++){
                    Gcity._m.removeClass('on',lis[i]);
                }
                Gcity._m.addClass('on',lis[this.count]);
                break;
            case 38: //向上箭头↑
                this.count--;
                if(this.count<0) this.count = len-1;
                for(i=0;i<len;i++){
                    Gcity._m.removeClass('on',lis[i]);
                }
                Gcity._m.addClass('on',lis[this.count]);
                break;
            case 13: // enter键
                this.input.value = Gcity.regExChiese.exec(lis[this.count].innerHTML)[0];
                Gcity._m.addClass('hide',this.ul);
                Gcity._m.addClass('hide',this.ul);
                /* IE6 */
                Gcity._m.addClass('hide',this.myIframe);
                break;
            default:
                break;
        }
    },

    /* *
     * 下拉列表的li事件
     * @ liEvent
     * */

    liEvent:function(){
        var that = this;
        var lis = Gcity._m.$('li',this.ul);
        for(var i = 0,n = lis.length;i < n;i++){
            Gcity._m.on(lis[i],'click',function(event){ 
                event = Gcity._m.getEvent(event);
                var target = Gcity._m.getTarget(event);
                that.input.value = Gcity.regExChiese.exec(target.innerHTML)[0];
                Gcity._m.addClass('hide',that.ul);
                /* IE6 下拉菜单点击事件 */
                Gcity._m.addClass('hide',that.myIframe);
            });
            Gcity._m.on(lis[i],'mouseover',function(event){
                event = Gcity._m.getEvent(event);
                var target = Gcity._m.getTarget(event);
                Gcity._m.addClass('on',target);
            });
            Gcity._m.on(lis[i],'mouseout',function(event){
                event = Gcity._m.getEvent(event);
                var target = Gcity._m.getTarget(event);
                Gcity._m.removeClass('on',target);
            })
        }
    }
};
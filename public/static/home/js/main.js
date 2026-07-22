
$(function() {
	
	$('body').on('click','.poplayer_quickplay .qp_pop_close',function(){
		$('.poplayer_quickplay').removeClass('showShodw').addClass('hideShodw');
		setTimeout(function(){
			$('.poplayer_quickplay').remove();
		},200)

	})
	
	

	$('.top_video .img_box').click(function(){
	    $(this).hide().siblings().removeClass('hide')
	    $(this).hide().siblings().find('video').trigger('play');
	})
	
	
	
	// 
	$('.evalu-list li').each(function(){
		var videoSrc = $(this).find('video').attr('src')
		// console.log(videoSrc)
		if(videoSrc =='' || videoSrc =='https://www.zhiranbj.com'){
		    // console.log('无视频')
			$(this).find('.img-btn').hide();
		}
	})
	// $('.header-top-right li.top-tel').hover(function(){
	// 	$(this).find('.tel-menu').stop().toggle()
	// })
	
// 
    
    $(".lazy").lazyload({placeholder:'https://www.zhiranbj.com/_templets_/default/images/bgTm.png',threshold: 100, effect : "fadeIn",failure_limit : 20,skip_invisible : false})
	
     
     
    $('body').click(function(){
    	$('html,body').trigger("scroll");
    })
    
    
    $('video').mediaelementplayer({
        success: function(player, node) {
            $('#' + node.id + '-mode').html('mode: ' + player.pluginType);
        }
    });
    // new WOW().init();
    $('.top').on('click', function(event) {
        $("html,body").animate({ scrollTop: 0 }, 500);
    });

    function nav() {
        var $mark = $(".nav .active");
        var $left = $mark.position().left + 14;
        var $thisWidth = $mark.find('a').width();
        $(".line").css({ 'left': $left, 'width': $thisWidth });
        $(".nav li").hover(function() {
            var $width = $(this).find('a').width();
            $(".line").stop();
            $(".line").animate({ left: $(this).find('a').position().left + 'px', width: $width }, 250);
        }, function() {
            $(".line").stop();
            $(".line").animate({ left: $mark.position().left + 14 + 'px', width: $thisWidth }, 550);
        });
    };
    nav();

});

// 滚动页面事件
var finished = true;

$(window).scroll(function() {
    (function() {
        var totalheight = $(window).height() + $(window).scrollTop();
        var documentheight = parseFloat($(document).height());

        // if (documentheight - totalheight <= 600) {
        //     $('.top').show();
        // } else {
        //     $('.top').hide();
        // };
        if($(window).scrollTop()>600){
        	$('.top').show();
        }else{
        	$('.top').hide();
        }
    })();
    (function() {
        var a = document.getElementById("number-list").offsetTop;
        if (a >= $(window).scrollTop() && a < ($(window).scrollTop() + $(window).height())) {
            if (finished === true) {
                number();
                finished = false;
            }
        }
    })();


});

(function() {
    $.fn.numberRock = function(options) {
        var defaults = {
            lastNumber: 100,
            duration: 2000,
            easing: 'swing'
        };
        var opts = $.extend({}, defaults, options);
        $(this).animate({
            num: "numberRock",
        }, {
            duration: opts.duration,
            easing: opts.easing,
            complete: function() {

            },
            step: function(a, b) {
                $(this).html(parseInt(b.pos * opts.lastNumber));
            }
        });
    }
})();
(function(){
	var tel = [31075898,31075899,31075904,31075905,31075910,31075911,31075916,31075917,31075916,31075917,31075916,31075917,31075916,31075917]
	var rand=Math.floor(Math.random()*10);
	var newTel = tel[rand];
	if(newTel){
		$('.sq_telphone').html('021-'+newTel)
	}
})();
// 报价
$(function() {
	
    // var test6 = new Vcity.CitySelector({ input: 'ks_start_city'});
    // var test7 = new Vcity.CitySelector({ input: 'ks_end_city'});
    // $('#Popup').on('click', function(event) {
    //     event.stopPropagation();
    //     /* Act on the event */
    // });
	

    $('.close-popup').on('click', function() {
    
        $('#Popup').removeClass('show');
        $('.popup-box').removeClass('show');
        $('body').stop().removeClass('uiw')
    });

    $('.getBaojia').on('click', function(event) {
        $('#Popup').addClass('show');
        $('.popup-box').addClass('show');
        $('body').stop().addClass('uiw')
    });
	$('.getBaojia_ks').on('click', function(event) {
        $('#Popup').addClass('show');
        $('.popup-box').addClass('show');
        $('body').stop().addClass('uiw')
        $('.left-nav li').eq(1).addClass('active').siblings().removeClass('active');
        $('.box1').eq(1).fadeIn(400).siblings().fadeOut(300);
    });
	$('.getBaojia_cg').on('click', function(event) {
        $('#Popup').addClass('show');
        $('.popup-box').addClass('show');
        $('body').stop().addClass('uiw');
        $('.left-nav li').eq(5).addClass('active').siblings().removeClass('active');
        $('.box1').eq(5).fadeIn(400).siblings().fadeOut(300);
        
    });
    $('.getBaojia_tc').on('click', function(event) {
        $('#Popup').addClass('show');
        $('.popup-box').addClass('show');
        $('body').stop().addClass('uiw');
        $('.left-nav li').eq(0).addClass('active').siblings().removeClass('active');
        $('.box1').eq(0).fadeIn(400).siblings().fadeOut(300);
    });
    $('.getBaojia_gq').on('click', function(event) {
        $('#Popup').addClass('show');
        $('.popup-box').addClass('show');
        $('body').stop().addClass('uiw');
        $('.left-nav li').eq(4).addClass('active').siblings().removeClass('active');
        $('.box1').eq(4).fadeIn(400).siblings().fadeOut(300);
    });
    $('.getBaojia_hg').on('click', function(event) {
        $('#Popup').addClass('show');
        $('.popup-box').addClass('show');
        $('body').stop().addClass('uiw');
        $('.left-nav li').eq(2).addClass('active').siblings().removeClass('active');
        $('.box1').eq(2).fadeIn(400).siblings().fadeOut(300);
    });
    $('.getBaojia_kd').on('click', function(event) {
        $('#Popup').addClass('show');
        $('.popup-box').addClass('show');
        $('body').stop().addClass('uiw');
        $('.left-nav li').eq(3).addClass('active').siblings().removeClass('active');
        $('.box1').eq(3).fadeIn(400).siblings().fadeOut(300);
        
    });
    // $('.popup-box').on('click', function() {
    //     $('#Popup').removeClass('show');
    //     $('.popup-box').removeClass('show');
    //     $('body').stop().removeClass('uiw')
    // });





    $('.tracking').on('click', function(event) {
        $('.track-box').addClass('show')
        $('#tracking').addClass('show')
    });
    $('.close-track').on('click', function(event) {
        $('.track-box').removeClass('show')
        $('#tracking').removeClass('show')
    });
    $('.track-box').on('click', function(event) {
        $('.track-box').removeClass('show')
        $('#tracking').removeClass('show')
    });
    $('#tracking').on('click', function(e) {
        e.stopPropagation();
    });

    $('.weixin').mouseout(function() {
        $('.ewm').removeClass('show')
    });
    $('.box1:eq(0)').show();
    $('.left-nav li:eq(0)').addClass('active');
    $(".left-nav li").click(function() {
        $(this).addClass('active').siblings().removeClass('active');
        var index = $(this).index();
        $('.box1:eq(' + index + ')').fadeIn(400).siblings().fadeOut(300);
    });
    $('.inside-nav li:eq(0)').addClass('active');
    $('.inside-nav li').click(function() {
        $(this).addClass('active').siblings().removeClass('active');
        var index = $(this).index();
        var liWidth = $('.container-3 .box2').innerWidth();
        $('.container-3').width(liWidth * $('.box2').length)
        var distance = -liWidth * index;
        $('.container-3').stop().animate({
            left: distance
        });
    });
    $('.gq-nav li:eq(0)').addClass('active');
    $('.gq-nav li').click(function() {
        $(this).addClass('active').siblings().removeClass('active');
        var index = $(this).index();
        var liWidth = $('.container-4 .box2').innerWidth();
        $('.container-4').width(liWidth * $('.box2').length)
        var distance = -liWidth * index;
        $('.container-4').stop().animate({
            left: distance
        });
    });
    $('.service li label').click(function(event) {
        $(this).parent('.service li').toggleClass('active');
    });
    $('.area-1').focus(function() {
        $(this).parent('div:after').css({
            'transform': 'rotate(180deg)'
        });
    });
    // cookie
    function FirstLoad() {
        var Storage = {};
        Storage.get = function(name) {
            return JSON.parse(localStorage.getItem(name))
        }
        Storage.set = function(name, val) {
            localStorage.setItem(name, JSON.stringify(val))
        }
        if (ifLoad()) {
            // 跳过
        } else {
            $('#Popup').addClass('show');
            $('.popup-box').addClass('show');
            $('body').stop().addClass('uiw');

        }

        function ifLoad() {
            var loaderTime = Storage.get('time');
            var timestamp = Date.parse(new Date());
            var setTime = 30000000;
            if (loaderTime) {
                if (parseInt(loaderTime) < Date.parse(new Date()) / 1000) {
                    var timestamp = timestamp / 1000 + setTime;
                    Storage.set("time", timestamp);
                    return false;
                }
                return true;
            } else {
                var timestamp = timestamp / 1000 + setTime;
                Storage.set("time", timestamp);
                return false;
            }
        }
    }
    setTimeout(function() {
        FirstLoad();
    }, 10000)
})

function getNowFormatDate() {
    var date = new Date();
    var seperator1 = "-";
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = year + seperator1 + month + seperator1 + strDate;
    $('.nowDate').html(currentdate)
}
getNowFormatDate();


// function set_cache(key, value) {
//     if (key == '') return false;
//     localStorage.setItem(key, value);
// }


$(function() {
    var timer;
    // var localCity = localStorage.getItem('thisCity');
    // if (localCity) {
    //     $('#city-toggle #city-now').html(localCity)
    // }
    $('.page-form-typeList p').on('click', function(event) {
        $('#form_banjiaType').val($(this).text())
    });
    $('.question-ul .question-li').click(function() {
        var $currentLi = $(this).closest('li'); // 使用 closest 以防 .question-li 不是直接子元素
    
        // 移除所有 li 元素的 show 类（收缩所有）
        $('.question-ul li').not($currentLi).removeClass('h-show');
        
        // 切换当前 li 元素的 show 类（如果已展开则收缩，如果已收缩则展开）
        $currentLi.stop().toggleClass('h-show');
    })



    $('.case-left-nav li').click(function() {
        var index = $(this).index();
        $(this).addClass('on').siblings().removeClass('on')
        $('.case-right-content .slide-content').eq(index).stop().addClass('show').siblings().stop().removeClass('show')
    })


    $('.case-left-nav li').hover(function(event) {
        var index = $(this).index();
        var $this = $(this);

        function way() {
            $this.addClass('on').siblings().removeClass('on')
            $('.case-right-content .slide-content').eq(index).stop().addClass('show').siblings().stop().removeClass('show')
        }
        timer = setTimeout(way, 200);
    }, function() {
        clearTimeout(timer);
    });


    $('.slide-top-nav1 .list').hover(function(event) {
        var index = $(this).index();
        var $this = $(this);

        function way() {
            $this.addClass('on').siblings().removeClass('on')
            $('.slide-bottom-content1 .slide-p').eq(index).stop().addClass('show').siblings().stop().removeClass('show')
        }
        timer = setTimeout(way, 200);
    }, function() {
        clearTimeout(timer);
    });


    $('.slide-top-nav2 .list').hover(function(event) {
        var index = $(this).index();
        var $this = $(this);

        function way() {
            $this.addClass('on').siblings().removeClass('on')
            $('.slide-bottom-content2 .slide-p').eq(index).stop().addClass('show').siblings().stop().removeClass('show')
        }
        timer = setTimeout(way, 200);
    }, function() {
        clearTimeout(timer);
    });

    $('.slide-top-nav3 .list').hover(function(event) {
        var index = $(this).index();
        var $this = $(this);

        function way() {
            $this.addClass('on').siblings().removeClass('on')
            $('.slide-bottom-content3 .slide-p').eq(index).stop().addClass('show').siblings().stop().removeClass('show')
        }
        timer = setTimeout(way, 200);
    }, function() {
        clearTimeout(timer);
    });

    $('.slide-top-nav6 .list').hover(function(event) {
        var index = $(this).index();
        var $this = $(this);

        function way() {
            $this.addClass('on').siblings().removeClass('on')
            $('.slide-bottom-content6 .slide-p').eq(index).stop().addClass('show').siblings().stop().removeClass('show')
        }
        timer = setTimeout(way, 200);
    }, function() {
        clearTimeout(timer);
    });
    
    $('.slide-top-nav7 .list').hover(function(event) {
        var index = $(this).index();
        var $this = $(this);

        function way() {
            $this.addClass('on').siblings().removeClass('on')
            $('.slide-bottom-content7 .slide-p').eq(index).stop().addClass('show').siblings().stop().removeClass('show')
        }
        timer = setTimeout(way, 200);
    }, function() {
        clearTimeout(timer);
    });



    $('.pack-nav .list').eq(0).addClass('on')
    $('.pack-nav .list').hover(function(event) {
        var index = $(this).index();
        var $this = $(this);

        function way() {
            $this.stop().addClass('on').siblings().removeClass('on');
            $('.pack-video-box .pack-video-box1').eq(index).stop().show().siblings().stop().hide()
        }
        timer = setTimeout(way, 200);
    }, function() {
        clearTimeout(timer);
    });





    $('.add_service_ul li').hover(function() {
        var index = $(this).data('list');
        $('.add_service_center_1').eq(index).stop().addClass('show').siblings().stop().removeClass('show')
    });

    $('.reasons .list').hover(function() {
        var index = $(this).data('list');
        $('.change-slide-box').addClass('show')
        $('.change-slide-box .slide-list').eq(index).stop().addClass('show').siblings().stop().removeClass('show')
    },function(){
        $('.change-slide-box .slide-list').removeClass('show')
        $('.change-slide-box').removeClass('show')
        
    });



    var timeLine = new Date().getTime(); //生成时间戳
	let timeBasis = timeLine.toString()

	timeBasis = timeBasis.slice(0, timeBasis.length - 3)
	timeBasis = parseInt(timeBasis / 16 - 100000000)
	timeBasis = parseInt(timeBasis / 10 / 4) //625秒更新一次
	timeBasis2 = parseInt(timeBasis / 2 / 4) //5000秒更新一次
    $("#count5").numberRock({
        lastNumber: $('#count5').text(),
        duration: 1000,
        easing: 'swing',
    });
    $("#count6").numberRock({
        lastNumber: $('#count6').text(),
        duration: 1000,
        easing: 'swing',
    });
    $("#count7").numberRock({
        lastNumber: $('#count7').text(),
        duration: 2000,
        easing: 'swing',
    });
    // $("#count8").numberRock({
    //     lastNumber: 768+timeBasis,
    //     duration: 1600,
    //     easing: 'swing',
    // });
    $("#count8").numberRock({
        lastNumber: $('#count8').text(),
        duration: 1600,
        easing: 'swing',
    });
    $("#count9").numberRock({
        lastNumber: 37680+timeBasis,
        duration: 2000,
        easing: 'swing',
    });
    $("#count10").numberRock({
        lastNumber: 6932+timeBasis2,
        duration: 2000,
        easing: 'swing',
    });
    $("#count11").numberRock({
        lastNumber: 12628+timeBasis2,
        duration: 2000,
        easing: 'swing',
    });
    $("#count12").numberRock({
        lastNumber: 6896+timeBasis,
        duration: 2000,
        easing: 'swing',
    });
    $("#count13").numberRock({
        lastNumber: 24217+timeBasis,
        duration: 2000,
        easing: 'swing',
    });
    $("#count14").numberRock({
        lastNumber: 9685+timeBasis2,
        duration: 2000,
        easing: 'swing',
    });
    $("#count15").numberRock({
        lastNumber: 23689+timeBasis,
        duration: 2000,
        easing: 'swing',
    });
    $("#count16").numberRock({
        lastNumber: 6437+timeBasis,
        duration: 2000,
        easing: 'swing',
    });
    $("#count17").numberRock({
        lastNumber: 7621+timeBasis,
        duration: 2000,
        easing: 'swing',
    });
    $("#count18").numberRock({
        lastNumber: 17699+timeBasis,
        duration: 2000,
        easing: 'swing',
    });
})




function browserRedirect() {
    var sUserAgent = navigator.userAgent.toLowerCase();
    var bIsIpad = sUserAgent.match(/ipad/i) == "ipad";
    var bIsIphoneOs = sUserAgent.match(/iphone os/i) == "iphone os";
    var bIsMidp = sUserAgent.match(/midp/i) == "midp";
    var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";
    var bIsUc = sUserAgent.match(/ucweb/i) == "ucweb";
    var bIsAndroid = sUserAgent.match(/android/i) == "android";
    var bIsCE = sUserAgent.match(/windows ce/i) == "windows ce";
    var bIsWM = sUserAgent.match(/windows mobile/i) == "windows mobile";

    if (bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM) {
        window.location.href = 'https://m.zhiranbj.com';
    }
}
// browserRedirect()

// 视频弹框
	function showVideo(videoName,videoSrc){
		// $('.showPack').click(function(){
    		$('body').append("<div class='poplayer_quickplay'><div class='quickplay_container'><div class='quickplay_header df js ac'><p>"+videoName+"</p><a class='qp_pop_close'>×</a></div><video src='"+videoSrc+"' controls='controls' width='640'></video></div></div>");
    		$('.poplayer_quickplay').addClass('showShodw');
    		setTimeout(function(){
    			$('.poplayer_quickplay video').mediaelementplayer({
			        success: function(player, node) {
			            $('#' + node.id + '-mode').html('mode: ' + player.pluginType);
			        }
			    });
			    $('.poplayer_quickplay video').trigger('play')
    		},100)

    	// })
}
//禁止滚动条滚动
function unScroll() {
    var top = $(document).scrollTop();
    $(document).on('scroll.unable',function (e) {
        $(document).scrollTop(top);
    })
}
//移除禁止滚动条滚动
function removeUnScroll() {
    $(document).unbind("scroll.unable");
}


function stopRightClick(){
	return false;
}
document.oncontextmenu=stopRightClick;
window.addEventListener('keydown', function (e) {
    if(e.keyCode == 83 && (navigator.platform.match('Mac') ? e.metaKey : e.ctrlKey)){
        e.preventDefault();
    }
})


$(function() {

    var swiper = new Swiper('.case-swiper', {
        slidesPerView: 3,
        spaceBetween: 30,
        freeMode: true,
    });
	
	var winTop = $('.suspension').offset().top + 350;

	$(window).scroll(function() {
		var totalheight = $(window).height() + $(window).scrollTop();
    	var documentheight = parseFloat($(document).height());
		// console.log("文档高度:"+documentheight)
		// console.log("滚动:"+$(window).scrollTop())
		if($(window).scrollTop() >= winTop && documentheight - totalheight >= 600 && documentheight > 3200){
			$('.suspension').addClass('fixed').removeClass('abso')
		}else if( documentheight - totalheight <= 600 && documentheight > 3200){
			$('.suspension').removeClass('fixed').addClass('abso')
		}else if( documentheight - totalheight >= 600){
			$('.suspension').removeClass('abso fixed')
		}else{
			$('.suspension').removeClass('fixed abso')
		}
	
	})
	

    $('.case-list').each(function() {
        var len = $(this).find('.swiper-slide').length;
        $(this).find('.tips span').html(len)
    })

    $('.form-box .form-nav .type-nav').click(function() {
        $(this).addClass('on').siblings().removeClass('on');
        var index = $(this).index();
        $('.input-box .input-slide').eq(index).addClass('show').siblings().removeClass('show')
    })


    var swiper = new Swiper('.recommend-case', {
        slidesPerView: 2,
        spaceBetween: 15,
        pagination: {
            el: '.recommend-case .swiper-pagination',
            clickable: true,
        },
    });


    $('.video-box .img-box.video').click(function(event) {
        $(this).hide();
        $(this).parents('.left-con').find('video').trigger('play');
    });


    $('.right-con .preview-img.video').on('click', function() {
        var src = $(this).data('src');
        $('#video-play').prop('src', src);
        $(this).parent('.right-con').siblings().find('.img-box').hide();
        $('#video-play').trigger('play');
    })

    $('.right-con .preview-img.img').on('click', function() {
        var src = $(this).data('src');
        $('#imgPreview').prop('src', src);
    })

    $('.video-nav li').click(function(event) {
        $(this).addClass('on').siblings().removeClass('on');
        var index = $(this).index();
        $('.border .box-1').eq(index).fadeIn().siblings().hide();
    });

    $('.question-list .list').each(function(index, ele) {
      $(this).find('i').html(index+1)
    });



  

   

     // 同城表单
    $('#tcForm').submit(function(e) {
        var cityStart = $('#tcStart').val();
        var cityEnd = $('#tcEnd').val();
        var phone = $('#tcPhone').val();
        if (cityStart == '') {
            $("#tcStart").focus();
            layer.tips("请选择起始城市", '#tcStart', {
                tips: [2, '#fe9901']
            });
            return false;
        }
        if (cityEnd == '') {
            $("#tcEnd").focus();
            layer.tips("请选择目的国家", '#tcEnd', {
                tips: [2, '#fe9901']
            });
            return false;
        }
        if (phone == '' || !checkPhone(phone)) {
            $("#tcPhone").focus();
            layer.tips("请正确输入手机号", '#tcPhone', {
                tips: [2, '#fe9901']
            });
            return false;
        }
        var Lload = layer.load(1, { shade: [0.1, '#fff'] });
        $.ajax({
            cache: true,
            dataType: 'json',
            crossDomain: true,
            type: "POST",
            url: 'https://api.yifeng.com/admin/api/web_zixun/pass/yifeng',
            data: $(this).serialize() + "&banjia_type=同城搬家&source=pc_lianxi&page=" + replaceurl(location.href) + "&referrer=" + encodeURIComponent(replaceurl(encodeURI(encodeURI(document.referrer)))),
            async: true,
            error: function(XmlHttpRequest, textStatus, errorThrown) {
                alert(XmlHttpRequest.responseText);
            },
            success: function(data) {
                layer.close(Lload);
                if (data.status == 'true') {
                    $('#tcForm')[0].reset();
                    layer.msg(data.msg, { icon: 1 });
                } else {
                    layer.msg(data.msg, { icon: 2 });
                }
            }
        });
        return false;

    });
    // 跨市表单
    $('#ksForm').submit(function(e) {
        var cityStart = $('#ksStart').val();
        var cityEnd = $('#ksEnd').val();
        var phone = $('#ksPhone').val();
        if (cityStart == '') {
            $("#ksStart").focus();
            layer.tips("请选择起始城市", '#ksStart', {
                tips: [2, '#fe9901']
            });
            return false;
        }
        if (cityEnd == '') {
            $("#ksEnd").focus();
            layer.tips("请选择目的国家", '#ksEnd', {
                tips: [2, '#fe9901']
            });
            return false;
        }
        if (phone == '' || !checkPhone(phone)) {
            $("#ksPhone").focus();
            layer.tips("请正确输入手机号", '#ksPhone', {
                tips: [2, '#fe9901']
            });
            return false;
        }
        var Lload = layer.load(1, { shade: [0.1, '#fff'] });
        $.ajax({
            cache: true,
            dataType: 'json',
            crossDomain: true,
            type: "POST",
            url: 'https://api.yifeng.com/admin/api/web_zixun/pass/yifeng',
            data: $(this).serialize() + "&banjia_type=跨市搬家&source=pc_lianxi&page=" + replaceurl(location.href) + "&referrer=" + encodeURIComponent(replaceurl(encodeURI(encodeURI(document.referrer)))),
            async: true,
            error: function(XmlHttpRequest, textStatus, errorThrown) {
                alert(XmlHttpRequest.responseText);
            },
            success: function(data) {
                layer.close(Lload);
                if (data.status == 'true') {
                    $('#ksForm')[0].reset();
                    layer.msg(data.msg, { icon: 1 });
                } else {
                    layer.msg(data.msg, { icon: 2 });
                }
            }
        });
        return false;

    });

    //检验手机号码是否正确
    function checkPhone(phone) {
        if (!(/^1[123456789]\d{9}$/.test(phone))) {
            return false;
        }
        return true;
    }
    //规则替换
    function replaceurl(url) {
        return url.replace(/\?/g, "**1**").replace(/\&/g, "**2**").replace(/\%/g, "**3**").replace(/\//g, "**4**");
    }
})
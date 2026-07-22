$('.menuBtn').append('<b></b><b></b><b></b>');
$('.menuBtn').on('click', function (e) {
    e.stopPropagation();
    $(this).toggleClass('open');
    $("#m_nav").toggleClass("act");
  });

  $("#m_nav a").click(function () {
    $('.menuBtn').removeClass("open")
    $("#m_nav").removeClass("act");
  })
  $(".nav_mask").click(function () {
    $('.menuBtn').removeClass("open")
    $("#m_nav").removeClass("act");
    console.log(1)
  })

// 手机导航
// $('.menuBtn').append('<b></b><b></b><b></b>');
// $('.menuBtn').click(function(event) {
//     $(this).toggleClass('open');
//     var _winw = $(window).width();
//     var _winh = $(window).height();
//     if ($(this).hasClass('open')) {
//         $('body').addClass('open');
//         if (_winw <= 1200) {
//             $('.hd-r').stop().slideDown();
//         }
//     } else {
//         $('body').removeClass('open');
//         if (_winw <= 1200) {
//             $('.hd-r').stop().slideUp();
//         }
//     }
// });
// $(window).on('resize', function(e) {
//     if ($(window).width() > 1200) {
//         $('.menuBtn').removeClass('open');
//         $('.hd-r').css('display', '');
//     }
// });

// 导航
// if ($(".nav li").find('dl').length) {
//     $(".nav li").find("dl").siblings("a").attr("href", "javascript:;")
// };

// function myNav() {
//     var _winw = $(window).width();
//     if (_winw >= 1200) {
//         $('.nav li').bind('mouseenter', function() {
//             $(this).find('dl').stop().slideDown("fast");
//             if ($(this).find('dl').length) {
//                 $(this).addClass('on');
//             }
//         });
//         $('.nav li').bind('mouseleave', function() {
//             $(this).removeClass('on');
//             $(this).find('dl').stop().slideUp("fast");
//         });
//         $('.nav dd').bind('mouseenter', function() {
//             $(this).find('.down').stop().slideDown("fast");
//             if ($(this).find('.down').length) {
//                 $(this).addClass('ok');
//             }
//         });
//         $('.nav dd').bind('mouseleave', function() {
//             $(this).removeClass('ok');
//             $(this).find('.down').stop().slideUp("fast");
//         });
//         $('body,.menuBtn').removeClass('open');
//     } else {
//         $(".nav .v1").click(function(e) {
//             $(this).parent("li").toggleClass('on');
//             var li = $(this).parent("li");
//             if (li.hasClass('on')) {
//                 li.parents(".nav").find('li').removeClass('on');
//                 li.addClass('on');
//                 li.parents(".nav").find("dl").stop().slideUp("fast");
//                 li.find("dl").stop().slideDown("fast");
//             } else {
//                 li.removeClass('on');
//                 li.find("dl").stop().slideUp("fast");
//             };
//         })
//     }
// }
$('.header>h5').on('click', function (e) {
    $("#m_nav").toggleClass("act");
    $('.menuBtn').toggleClass('open');
  });
$("#m_nav  .title .list").css({ "opacity": "1", "visibility": "visible" });
$("#m_nav  .title i ").click(function () {
  var tt = $(this).parents(".title");
  if ($(tt).hasClass('ons')) {
    $(tt).children(".list").slideUp(600);
    $(tt).removeClass("ons");
  } else {
    $("#m_nav .title").removeClass("ons");
    $("#m_nav .title .list").slideUp();
    $(tt).children(".list").slideDown();
    $(tt).toggleClass("ons");
  }
});
function myNav() {  
    var _winw = $(window).width();  
    if (_winw >= 1200) {  
        $('.nav .m_nav_pclist li').on('mouseenter', function() {  
            var $this = $(this);  
            $this.find('dl').stop().slideDown("fast");  
            // 只在原本没有 on 类名的情况下添加  
            if (!$this.hasClass('on')) {  
                $this.addClass('on-dynamic'); // 使用 on-dynamic 代替 on 以区分动态添加的  
            }  
        });  
  
        $('.nav .m_nav_pclist li').on('mouseleave', function() {  
            var $this = $(this);  
            // 移除动态添加的 on-dynamic 类名  
            if ($this.hasClass('on-dynamic')) {  
                $this.removeClass('on-dynamic');  
            }  
            $this.find('dl').stop().slideUp("fast");
        });  
  
        // 类似地，对于 dd 的处理...  
        $('.nav .m_nav_pclist dd').on('mouseenter', function() {  
            var $this = $(this);  
            $this.siblings().removeClass('ok').find('.down').slideUp('fast');
            $this.find('.down').stop().slideDown("fast");  
            if (!$this.hasClass('ok')) {  
                $this.addClass('ok-dynamic'); // 使用 ok-dynamic 代替 ok  
            }  
        });
  
        // $('.nav dd').on('mouseleave', function() {  
        //     var $this = $(this);  
        //     if ($this.hasClass('ok-dynamic')) {  
        //         $this.removeClass('ok-dynamic');  
        //     }
        //     $this.find('.down').stop().slideUp("fast");
        // });
  
        $('body,.menuBtn').removeClass('open');  
    } 
    // else {  
    //     $(".nav .v1").click(function(e) {  
    //         e.preventDefault(); // 如果需要阻止默认行为，比如链接的跳转  
    //         var $li = $(this).parent("li");  
    //         $li.toggleClass('on');  
    //         // 这里的逻辑保持不变，因为点击事件通常用于切换状态  
    //         // ...  
    //     });  
    // }
    else{
        $(".nav .tit i").click(function(e) {
            $(this).parents("li").toggleClass('on');
            var li = $(this).parents("li");
            if (li.hasClass('on')) {
                li.parents(".nav").find('li').removeClass('on');
                li.addClass('on');
                li.parents(".nav").find("dl").stop().slideUp("fast");
                li.find("dl").stop().slideDown("fast");
            } else {
                li.removeClass('on');
                li.find("dl").stop().slideUp("fast");
            };
        })
    }  
} 

myNav();
$(window).resize(function(event) {
    myNav();
    $('.menuBtn').removeClass('open');
});
$(function() {
    // function changeTab() {
    //     var getTabId = $('.tabs-header .active a').attr('tab-id');
    //     $('.tab').stop().fadeOut(100, function () {
    //         $(this).removeClass('active');
    //     }).hide();
    //     $('.tab[tab-id=' + getTabId + ']').stop().fadeIn(150, function () {
    //         $(this).addClass('active');
          
    //     });
    // };
    // $('.tabs-header a').hover(function (e) {
    //     e.preventDefault();
    //     var tabId = $(this).attr('tab-id');
    //     $('.tabs-header a').stop().parent().removeClass('active');
    //     $(this).stop().parent().addClass('active');
    //     changePos();
    //     tabCurrentItem = tabItems.filter('.active');
    //     $('.tab').stop().fadeOut(100, function () {
    //         $(this).removeClass('active');
    //     }).hide();
    //     $('.tab[tab-id="' + tabId + '"]').stop().fadeIn(150, function () {
    //         $(this).addClass('active');
            
    //     });
    //     Color();
    // });

    // var tabItems = $('.tabs-header ul li');
    // var tabCurrentItem = tabItems.filter('.active');
    $('#next').on('click', function (e) {
        e.preventDefault();
        var nextItem = tabCurrentItem.next();
        tabCurrentItem.removeClass('active');
        if (nextItem.length) {
           tabCurrentItem = nextItem.addClass('active');
        } else {
            tabCurrentItem = tabItems.first().addClass('active');
        }
        changePos();
        changeTab();
        Color();
    });
    $('#prev').on('click', function (e) {
        e.preventDefault();
        var prevItem = tabCurrentItem.prev();
        tabCurrentItem.removeClass('active');
        if (prevItem.length) {
            tabCurrentItem = prevItem.addClass('active');
        } else {
            tabCurrentItem = tabItems.last().addClass('active');
        }
        changePos();
        changeTab();
        Color();
    });
    // var activePos = $('.tabs-header .active a').position();
    // function changePos() {
    //     activePos = $('.tabs-header .active a').position();
    //     $('.tabs-header .border').stop().css({
    //         left: activePos.left,
    //         width: $('.tabs-header .active').width()
    //     });
    // };
    // changePos();
    // function Color(){
    //     var $first = $('.tabs-header ul li').eq(0);
    //     var $last = $('.tabs-header ul li').length - 1;
    //     var $last1 = $('.tabs-header ul li').eq($last);
    //     if($first.hasClass('active')){
    //         $('.clients-left').css({'color':'rgba(0,0,0,0.1)','pointer-events':'none'});
    //     }else{
    //         $('.clients-left').css({'color':'#000','pointer-events':'auto'});
    //     }
    //     if($last1.hasClass('active')){
    //         $('.clients-right').css({'color':'rgba(0,0,0,0.1)','pointer-events':'none'});
    //     }else{
    //         $('.clients-right').css({'color':'#000','pointer-events':'auto'});
    //     }
    // };
    // Color();
    
    $('.evalu-list:eq(0)').show();
    $('.Customer li').hover(function(event) {
        event.preventDefault();
        $(this).addClass('active').siblings().removeClass('active');
        var index = $(this).index();
        $('.evalu-list:eq(' + index + ')').addClass('show').siblings().removeClass('show')
        
        border3();
    });
    border3();
    function border3(){
        var $thisWidth = $('.Customer li.active').outerWidth();
        var $thisPosition = $('.Customer li.active').position();
        $('.border3').css({
            "width": $thisWidth,
            "left": $thisPosition.left + 10 +'px'
        });
    };
});


$('#index_case_tab li').eq(0).addClass('active');
$('#index_case_tab_item .evalu-list').eq(0).addClass('show');
var timer;
$('.Birthdays-left-nav li').hover(function(event) {
    var index = $(this).index();
    function way() {
        $('.Birthdays-right-box .slide').removeClass('show');
        $('.Birthdays-right-box .slide').eq(index).addClass('show');
        $('.Birthdays-left-nav li').removeClass('on');
        $('.Birthdays-left-nav li').eq(index).addClass('on');
    }
    timer = setTimeout(way, 200);
}, function() {
    clearTimeout(timer);
});


$('.nav-House-btn li').hover(function(event) {
    var index = $(this).index();
    function way() {
        $('.House-box .House-box-1').removeClass('show');
        $('.House-box .House-box-1').eq(index).addClass('show');
        $('.nav-House-btn li').removeClass('on');
        $('.nav-House-btn li').eq(index).addClass('on');
    }
    timer = setTimeout(way, 200);
}, function() {
    clearTimeout(timer);
});



// $('.nav-Culture-btn li').click(function(event) {
//     var index = $(this).index();
//     $('.nav-Culture-btn li').removeClass('on')
//     $(this).addClass('on');
//     $('#culture-ul').css("margin-left",-index+"00%");
// });


$('#culture-ul .culture-li').eq(0).addClass('on');
$('.nav-Culture-btn li').hover(function(event) {
    var index = $(this).index();
    $this = $(this);
    function way() {
        $('.nav-Culture-btn li').removeClass('on')
        $this.addClass('on');
        $('#culture-ul .culture-li').removeClass('on')
        $('#culture-ul .culture-li').eq(index).addClass('on');
        $('#culture-ul').css("margin-left",-index+"00%");
    }
    timer = setTimeout(way, 200);
}, function() {
    clearTimeout(timer);
});





// $('.nav-corner-btn li').click(function(event) {
//     var index = $(this).index();
//     $('.nav-corner-btn li').removeClass('on')
//     $(this).addClass('on');
//     $('.corner-box .corner-box-1').eq(index).addClass('show').siblings().removeClass('show')
// });
$('.nav-corner-btn li').hover(function(event) {
    var index = $(this).index();
    var $this = $(this);
    function way() {
        $('.nav-corner-btn li').removeClass('on')
        $this.addClass('on');
        $('.corner-box .corner-box-1').eq(index).addClass('show').siblings().removeClass('show')
    }
    timer = setTimeout(way, 200);
}, function() {
    clearTimeout(timer);
});







$('#cultrue-nav li').on('click', function(event) {
    var index = $(this).index();
    $(this).addClass('on').siblings().removeClass('on');
    $('#cultrue-box .cultrue-box-1').eq(index).addClass('show').siblings().removeClass('show')
});
$('#cultrue-nav2 li').on('click', function(event) {
    var index = $(this).index();
    $(this).addClass('on').siblings().removeClass('on');
    $('#cultrue-box2 .cultrue-box-1').eq(index).addClass('show').siblings().removeClass('show')
});
$('#cultrue-nav3 li').on('click', function(event) {
    var index = $(this).index();
    $(this).addClass('on').siblings().removeClass('on');
    $('#cultrue-box3 .cultrue-box-1').eq(index).addClass('show').siblings().removeClass('show')
});
$('#cultrue-nav4 li').on('click', function(event) {
    var index = $(this).index();
    $(this).addClass('on').siblings().removeClass('on');
    $('#cultrue-box4 .cultrue-box-1').eq(index).addClass('show').siblings().removeClass('show')
});
$('#cultrue-nav5 li').on('click', function(event) {
    var index = $(this).index();
    $(this).addClass('on').siblings().removeClass('on');
    $('#cultrue-box5 .cultrue-box-1').eq(index).addClass('show').siblings().removeClass('show')
});
$("body").undelegate("#searchInput", "keyup").delegate("#searchInput", "keyup", function() {
    var theName = $(this).val().replace(/\s+/g, "");
    console.log(theName)
    if(theName.length > 0) { 
        $("#dot-box .dot-list").each(function() {
        	var city = $(this).data('city').toString();
            if(city.indexOf(theName) > -1) {
                $(this).removeClass("hide").addClass('show')
            } else {
                $(this).addClass("hide").removeClass("show");
            }
        });
    } else { 
        $("#dot-box .dot-list").each(function() {
             $(this).removeClass("show hide");
        });
    }
});



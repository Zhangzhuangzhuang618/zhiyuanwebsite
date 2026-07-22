$('a[href*=#],area[href*=#]').click(function () {
    if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
      var $target = $(this.hash);
      $target = $target.length && $target || $('[name=' + this.hash.slice(1) + ']');
      if ($target.length) {
        var targetOffset = $target.offset().top-100;
        $('html,body').animate({
          scrollTop: targetOffset
        },
          1000);
        return false;
      }
    }
  });
$("#ggotop2").on("click", function () {
    $("html,body").stop().animate({
        scrollTop: 0
    }, 1000);
});
$("#ggotop").on("click", function () {
  $("html,body").stop().animate({
      scrollTop: 0
  }, 1000);
});
//    //移动端展开nav
//    $('#navToggle').on('click',function(){
//     $('.m_nav').addClass('open');
//   })
//   $('#menu-aside').on('click',function(){
//     $('.m_nav').addClass('open');
//   })
//   //关闭nav
//   $('#nav_closed').on('click',function(){
//     $('.m_nav').removeClass('open');
//   })
  
//  //二级导航  移动端
//  $(".m_nav .nav_list .nav_more").click(function() {
//   $(this).addClass('aad');
//  //  $(this).parents().find('li').addClass('ccd')
//    // $(this).siblings(".nav_son").slideToggle('slow').parents().siblings("li").children(".nav_son").slideUp('slow');
//    // $(this).toggleClass("active").parents().siblings("li").children("span").removeClass("active");
//    $(this).siblings(".nav_son").slideToggle(0).parents().siblings('li').find('.nav_son').hide();
//    $(this).toggleClass("active").parents().siblings("li").children("span").removeClass("active");
   
//    });
// //二级导航  移动端
// $(".m_nav .ul li span").click(function() {
//  $(this).addClass('aad');
//   $(this).siblings("div.dropdown_menu").slideToggle('slow').parents().siblings("li").children("div.dropdown_menu").slideUp('slow');
//   $(this).toggleClass("active").parents().siblings("li").children("span").removeClass("active");
  
//   });

    /**
* 添加cookie
 * @param {string}  objName cookie名
 * @param {string} objValue cookie值
 * @param {string} objHours cookie过期时间
 */
//添加cookie
function addCookie(objName,objValue,objHours){      
    var str = objName + "=" + encodeURI(objValue); if(objHours > 0){ 
      //为时不设定过期时间，浏览器关闭时cookie自动消失 
      var date = new Date(); 
      var ms = objHours*3600*1000;
       date.setTime(date.getTime() + ms);
      str += "; expires=" + date.toGMTString(); } 
      document.cookie = str; 
    }
    /**
  * 添加cookie方法2 encodeURL编码处理 PHP urldecode编码
   * @param {string}  name cookie名
   * @param {string} value cookie值
   * @param {string} time cookie过期时间
   */
    function setCookie(name, value, time){
    var nameString = name + '=' + encodeURI(value);
    var expiryString = "";
    if(time !== 0) {
        var expdate = new Date();
        if(time == null || isNaN(time)) time = 60*60*1000;
        expdate.setTime(expdate.getTime() +  time);
     expiryString = ' ;expires = '+ expdate.toGMTString();
     console.log(expiryString);
    }
    var path = " ;path =/";
    document.cookie = nameString + expiryString + path;
    }
  /**
  * 获取cookie
   * @param {string}  objName cookie名
   */
    function getCookie(objName){
        var arrStr = document.cookie.split("; "); 
        for(var i = 0;i < arrStr.length;i ++){ 
          var temp = arrStr[i].split("="); if(temp[0] == objName) return unescape(temp[1]); } 
    }
  /**
  * 是否存在cookie
   * @param {string}  name cookie名
   */
  function isSetcookie(name) {  
    var cookies = document.cookie.split("; ");  
    for (var i = 0; i < cookies.length; i++) {  
        var cookie = cookies[i];  
        var eqPos = cookie.indexOf("=");  
        var cookieName = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;  
        if (cookieName == name) {  
            return true;  
        }  
    }  
    return false;  
  }  
  
  /**
  * 是否存在cookie
   * @param {string}  key cookie名
   */
  function deleteCookie(key){
      setCookie({[key]: "" }, -1)
  }

  function pop_up(txt){
    $('.alert').html(txt).stop().fadeIn(500);
    setTimeout(function () {
        $(".alert").fadeOut(500);
    },4000)
  };

  $(document).ready(function(e) {
    //延时加载动画插件配置
    var wow = new WOW({
      boxClass: 'mx',
      animateClass: 'animated',
      offset: 0,
      mobile: true,
      live: true
    });
    wow.init();
     
  }); 

  // 语言切换
  function Language(value){
    $.ajax({
    type:'post',
    url:"/languageGo",
    data:'value='+value,
    dataType:'json',
    success:function(res){
      if(res.code==1){
        pop_up(res.msg)
         // window.location = "https://www.example.com";
        // location.replace("https://www.example.com");
        // setTimeout('window.location.replace("http://'+res.domain+'");',1000);
        setTimeout('window.location.reload()',2000);
        // layer.msg('语言切换成功', {icon: 1,time:1000});
      }else{
        // layer.msg('执行有误', {icon: 2,time:1000});
      }
    }
    })
}
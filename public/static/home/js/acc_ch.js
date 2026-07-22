function valueU(name,error){
    $(name).removeClass('input_focus');
    $(error).html('');
}
function pop_up(txt){
    $('.alert').html(txt).stop().fadeIn(500);
    setTimeout(function () {
        $(".alert").fadeOut(500);
    },2000)
  };

/**
 * 验证数字
 * @param {string}  value 值
 * @param {string} error 错误变量
 * @param {string} msg  错误说明标题，如电话、邮编等
 * @param {string} other  其他等
 *
 */
function checknumber(value,error,msg,other = $('#dd')) {
    //1.获取昵称

    var name = $.trim(value.val());
    //2.定义正则  itcast@163.com
    var reg_name =new RegExp("^[0-9-_]+$");
    //3.判断
    var flag = reg_name.test(name);
    if(name==''){
        error.html(msg+'不能为空');
        value.addClass('input_focus');
        other.addClass('input_focus');
        value.trigger( "select" );
    }else if(flag){
        value.removeClass('input_focus');
        other.removeClass('input_focus');
        error.html('');
    }else{
        error.html('只能输入数字和符号“-”，“_”');
        value.addClass('input_focus');
        other.addClass('input_focus');
        value.trigger( "select" );
    }
    // 4.返回校验是否通过
    return flag;
}
/**
 * 验证名称
 * @param {string}  value 值
 * @param {string} error 错误变量
 * @param {string} msg  错误说明标题，如姓名、地址等
 * @param {string} other  其他
 */
function checkTitle(value,error,msg,other = $('#dd')) {
    //1.获取昵称
    var name = $.trim(value.val());
    //2.定义正则  itcast@163.com
    var reg_name =new RegExp("^[A-Za-z0-9\u4e00-\u9fa5\-_]+$");
    //3.判断
    var flag = reg_name.test(name);
    if(name==''){
        error.html(msg+'不能为空');
        value.addClass('input_focus');
        other.addClass('input_focus');
        value.trigger( "select" );

    }else if(flag){
        value.removeClass('input_focus');
        other.removeClass('input_focus');
        error.html('');
    }else{
        error.html('只能输入中文、数字、英文和符号“-”，“_”');
        value.addClass('input_focus');
        other.addClass('input_focus');
        value.trigger( "select" );
    }
    // 4.返回校验是否通过
    return flag;
}

/**
 * 验证手机
 * @param {string}  value 值
 * @param {string} error 错误变量
 * @param {string} msg  错误说明标题，如姓名、地址等
 * @param {string} other 其他
 */
function checkTelephone(value,error,msg,other = $('#dd')) {
    //1.获取号码
    var phone = $.trim(value.val());
    //2.定义正则  itcast@163.com
    var reg_phone =/^1(3|4|5|6|7|8|9)\d{9}$/;
    //3.判断
    var flag = reg_phone.test(phone);
    if(phone==''){
        error.html(msg+'不能为空');
        value.addClass('input_focus');
        other.addClass('input_focus');
        value.trigger( "select" );
    }else if(flag){
        value.removeClass('input_focus');
        other.removeClass('input_focus');
        error.html('');
    }else{
        error.html('手机号码格式不正确');
        value.addClass('input_focus');
        other.addClass('input_focus');
        value.trigger( "select" );
    }
    // 4.返回校验是否通过
    return flag;
}
/**
 * 验证不能为空
 * @param {string}  value 值
 * @param {string} error 错误变量
 * @param {string} msg  错误说明标题
 * @param {string} other  其他
 *
 */
function checkNull(value,error,msg,other = $('#dd')) {
    var info = $.trim(value.val());
    if(info==''){
        error.html(msg+'不能为空');
        value.addClass('input_focus');
        other.addClass('input_focus');
        value.trigger( "select" );
        return false;
    }else{
        value.removeClass('input_focus');
        other.removeClass('input_focus');
        error.html('');
        return true;}
}
/**
 * 验证多选框不能为空
 * @param {string}  input 值
 * @param {string} error 错误变量
 * @param {string} msg  错误说明标题
 *
 */
function checkCheckbox(input,error,msg){
    if(true===input.is(':checked')){
        error.html('');
        return true;
    }else{
        error.html(msg);
        return false;
    }
}
/**
 * 验证下拉菜单不能为空
 * @param {string}  value 值
 * @param {string} error 错误变量
 * @param {string} msg  错误说明标题
 *
 */
function checkSelect(value,error,msg){
    val=$(value+" option:selected").val()
    if(val==''){
        error.html(msg+'不能为空');
        return false;
    }else{
        error.html('');
        return true;
    }
}
//   captcha.onclick=function(){
//       this.src = "/verify";
//   }

/**
 * 验证邮箱
 * @param {string}  value 值
 * @param {string} error 错误变量
 *
 */
// 校验邮箱
function checkEmail(value,error) {
    //1.获取邮箱
    var email = $.trim(value.val());
    //2.定义正则  itcast@163.com
    //3.判断
    //    if(email!=''){
    var reg_email =new RegExp("^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$");
    var flag = reg_email.test(email);
    if(email==''){
        error.html('邮箱不能为空');
        value.addClass('input_focus');
        value.trigger( "select" );

    }else if(flag){
        value.removeClass('input_focus');
        error.html('');
    }else{
        error.html('邮箱格式不正确');
        value.addClass('input_focus');
        value.trigger( "select" );
    }
    // 4.返回校验是否通过
    return flag;
    //    }else{
    //     return true;
    //    }

}

function   m_form(){
    var data = $('#ME_form').serialize();

    if(
        checkName($('#M_title'),"姓名")   &&
        checknumber($('#M_phone'),'联系方式') &&
        checkEmail($('#M_email')) &&
        checkNull($('#M_content'),'详细需求')
        && checknumber($('#M_code'),'验证码')
    ){
        $.ajax({
            type:'post',
            url:"/GoMes",
            data:data,
            dataType:"json",
            success:function(res){
                if(res.code==1){
                    pop_up(res.msg)
                    setTimeout('window.location.reload()',2000);
                }else{
                    pop_up(res.msg);
                    captcha.src="/verify";
                }
            },
            error:function(res){

            }
        })
    }
}

// 报价表单
var FormConfig=[
    {num:0,title:'起始地址',input:$('#form_bj .start_address'),error:$('#form_bj .error-start-address')},
    {num:1,title:'目的地址',input:$('#form_bj .end_address'),error:$('#form_bj .error-end-address')},
    {num:2,title:'联系手机',input:$('#form_bj .form_tel'),error:$('#form_bj .error-tel')},
    {num:3,title:'验证码',input:$('#form_bj .form_code'),error:$('#form_bj .error-code')},
];

function for_form_submit(){
    if(
        checkNull(FormConfig[0]['input'],FormConfig[0]['error'],'')
        && checkNull(FormConfig[1]['input'],FormConfig[1]['error'],'')
        && checkTelephone(FormConfig[2]['input'],FormConfig[2]['error'],'')
        &&  checknumber(FormConfig[3]['input'],FormConfig[3]['error'],'')

    ){
        request_apply('/GoAssess',$('#form_bj').serializeArray(),'form_bj');
    }
}


// 公有报价表单
var FormConfig2=[
    {num:0,title:'起始地址',input:$('#form_bj2 .start_address'),error:$('#form_bj2 .error-start-address')},
    {num:1,title:'目的地址',input:$('#form_bj2 .end_address'),error:$('#form_bj2 .error-end-address')},
    {num:2,title:'联系手机',input:$('#form_bj2 .form_tel'),error:$('#form_bj2 .error-tel')},
    {num:3,title:'验证码',input:$('#form_bj2 .form_code'),error:$('#form_bj2 .error-code')},
];

function for_form_submit2(){
    if(
        checkNull(FormConfig2[0]['input'],FormConfig2[0]['error'],'')
        && checkNull(FormConfig2[1]['input'],FormConfig2[1]['error'],'')
        && checkTelephone(FormConfig2[2]['input'],FormConfig2[2]['error'],'')
        &&  checknumber(FormConfig2[3]['input'],FormConfig2[3]['error'],'')

    ){
        request_apply('/GoAssess',$('#form_bj2').serializeArray(),'form_bj2');
    }
}

function selectValue(val){
   $('#form_page_bj .form_select0').val(val)
}


// 产品页报价表单
var FormConfig3=[
    {num:0,title:'起始地址',input:$('#form_page_bj .start_address'),error:$('#form_page_bj .error-start-address')},
    {num:1,title:'目的地址',input:$('#form_page_bj .end_address'),error:$('#form_page_bj .error-end-address')},
    {num:2,title:'联系手机',input:$('#form_page_bj .form_tel'),error:$('#form_page_bj .error-tel')},
    {num:3,title:'验证码',input:$('#form_page_bj .form_code'),error:$('#form_page_bj .error-code')},
];

function for_form_submit3(){
    if(
        checkNull(FormConfig3[0]['input'],FormConfig3[0]['error'],'')
        && checkNull(FormConfig3[1]['input'],FormConfig3[1]['error'],'')
        && checkTelephone(FormConfig3[2]['input'],FormConfig3[2]['error'],'')
        &&  checknumber(FormConfig3[3]['input'],FormConfig3[3]['error'],'')

    ){
        request_apply('/GoAssess',$('#form_page_bj').serializeArray(),'form_page_bj');
    }
}
var page_search_product_Config=[
    {num:0,title:'产品关键词',input:$('#page_search_product .form_keyword'),error:$('#page_search_product .error-keyword'),hidden:''},
    {num:1,title:'产品分类',input:$('#page_search_product .form_type')},
];
/**
 *  内页产品搜索
 */
function page_search_product(page = 1){
    if(checkTitle(page_search_product_Config[0]['input'],page_search_product_Config[0]['error'],page_search_product_Config[0]['title'])){
        var data = $('#page_search_product').serialize();
        // $.each(data,function(index,item){
        //     console.log(item)
        //     // data[index].value=$.trim(item.value);
        // })
        $.ajax({
            type:'post',
            url:'/PageSearch',
            data:{
                'form_keyword' : $.trim(page_search_product_Config[0]['input'].val()),
                'form_type' : $.trim(page_search_product_Config[1]['input'].val()),
                'page' : $.trim(page)
            },
            dataType:"json",
            success:function(res){
                if(res.code==200){
                    if(res.total > 0){
                        html = '';
                        $.each(res.datalist,function(index,item){
                            html += '<li> <a href="'+item.link+'" target="'+item.target+'" class="con">';
                            html += '<div class="pic"><img alt="'+item.title+'" src="'+item.image+'"></div>';
                            html += '<div class="txt"><h3 class="tit">'+item.title+'</h3></div> </a></li>';
                        });
                        $('#page_product_html').html(html);
                        $('#page_product_page').html(res.page);
                        var id = '';
                        $.each($('.pagination a'),function(){
                            var href = $(this).attr('href');
                            var regex = /page=([0-9]+)/;
                            var match = href.match(regex);
                            if (match && match.length > 1) {
                                id = match[1]; // 这是提取出的id值
                                $(this).attr('href','javascript:;')
                                $(this).attr('onclick','page_search_product('+id+')')
                            }
                        });
                    }else{
                        $('#page_product_html').html('<p style="font-size:15px;">未检索到相关产品，建议换个关键词搜索</p>');
                    }
                }
            },
            error:function(res){
                console.log('请求有误~'+res);
            }
        })
    }
}

var IndexConfig=[
    {num:0,title:'',input:$('#index_message_form .form_type'),error:$('#index_message_form .error-type'),hidden:''},
    {num:1,title:'',input:$('#index_message_form .form_name'),error:$('#index_message_form .error-name'),hidden:''},
    {num:2,title:'',input:$('#index_message_form .form_tel'),error:$('#index_message_form .error-tel')},
    {num:3,title:'',input:$('#index_message_form .form_code'),error:$('#index_message_form .error-code')},
    {num:4,title:'',input:$('#index_message_form .form_content'),error:$('#index_message_form .error-content'),hidden:''},
];
/**
 * 首页留言
 */
function index_message_form(){

    if(
        checkTitle(IndexConfig[0]['input'],IndexConfig[0]['error'],IndexConfig[0]['title'],$('#index_message_form .form_type_box'))
        && checkTitle(IndexConfig[1]['input'],IndexConfig[1]['error'],IndexConfig[1]['title'],$('#index_message_form .form_name_box'))
        && checkTelephone(IndexConfig[2]['input'],IndexConfig[2]['error'],$('#index_message_form .form_tel_box'))
        &&  checknumber(IndexConfig[3]['input'],IndexConfig[3]['error'],'验证码',$('#index_message_form .form_code_box'))
        && checkTitle(IndexConfig[4]['input'],IndexConfig[4]['error'],IndexConfig[4]['title'],$('#index_message_form .form_content_box'))

    ){
        alert(1)
        request_apply('/GoAssess',$('#index_message_form').serializeArray(),'one_form_b');
    }
}

function form_type(name){
    $('#index_message_form #form_type').val(name);
    $('.form_type_box').removeClass('input_focus');$('.error-type').html('');
}
/**
 *  请求
 * @param {string}  url 请求地址
 * @param {string} data 表单数据
 * @param {string} formID 表单ID
 */
function request_apply(url,data,formID){

    $.each(data,function(index,item){
        data[index].value=$.trim(item.value);
    })
    $.ajax({
        type:'post',
        url:url,
        data:data,
        dataType:"json",
        success:function(res){
            console.log(res);
            if(res.code==200){
                pop_up(res.msg);
                localStorage.removeItem(formID);
                deleteCookie('localStorageData');
                setTimeout('window.location.reload()',2000);
            }else{
                pop_up(res.msg);
                addCookie('localStorageData',formID);
                localStorage.setItem(formID, JSON.stringify($('#'+formID).serializeArray()));
            }
        },
        error:function(res){
            console.log('请求有误~'+res);
        }
    })
}
/**
 * 获取手机验证码
 * @param {string}  tel 手机号码
 * @param {string} hidden 唯一标识
 * @param {string} button 唯一标识
 * @param {string} formID 表单ID
 */
function get_sms(tel,hidden,button,formID){
    $.ajax({
        type:'post',
        url:"/GetSmsCode",
        data:{
            form_email:tel,
            __ems__:hidden
        },
        dataType:"json",
        success:function(res){
            // res=JSON.parse(res);
            console.log(res)

            if(res.code==200){
                pop_up(res.msg);
                curCount1 = count;
                InterValObj1=setInterval(()=>{
                    SetRemainTime1(button);
                }, 1000);
                localStorage.removeItem(formID);
                deleteCookie('localStorageData');
            }else if(res.code==401){
                pop_up(res.msg);
                addCookie('localStorageData',formID);
                localStorage.setItem(formID, JSON.stringify($('#'+formID).serializeArray()));
            }else{
                pop_up(res.msg);
                addCookie('localStorageData',formID);
                localStorage.setItem(formID, JSON.stringify($('#'+formID).serializeArray()));
            }
        },
        error:function(res){
            console.log('请求有误');
        }
    })
}


var count = 60; //间隔函数，1秒执行
var InterValObj1; //timer变量，控制时间
var curCount1;//当前剩余秒数
/**
 * 60秒倒计时重新获取短信
 * @param {string}  button 发送短信的按钮
 */
function SetRemainTime1(button) {
    if (curCount1 == 0) {
        window.clearInterval(InterValObj1);//停止计时器
        button.html("重新发送");
        button.removeAttr("disabled");
        button.removeClass('btn-disable');
    }
    else {
        curCount1--;
        button.addClass('btn-disable');
        button.attr("disabled","disabled");
        button.html( + curCount1 + "秒再获取");
    }
}

// 从 localStorage 获取数据并填充表单
window.onload = function() {
    if(isSetcookie('localStorageData')){
        data=getCookie('localStorageData');
        // console.log(data);
        var formData = JSON.parse(localStorage.getItem(data));
        // console.log(formData);
        if (formData) {
            $.each(formData, function(index, field) {
                $('.form_select0 option').each(function() {
                    if ($(this).text() === field.value) {
                      $(this).prop('selected', true);
                      return false; // 退出循环
                    }
                  });
                $('.form_input'+index).val(field.value);
            });
        }
    }

};

var HeaderConfig=[
    {num:0,title:'搜索关键词',input:$('#header_form_pc .form_keyword'),error:$('#header_form_pc .error-keyword'),hidden:''},
    {num:1,title:'搜索类型',input:$('#header_form_pc .form_type')},
];
function search_go(){
    if(checkTitle(HeaderConfig[0]['input'],HeaderConfig[0]['error'],HeaderConfig[0]['title'])){
        var keyword = $.trim(HeaderConfig[0]['input'].val());
        var f=document.createElement('form');
        f.style.display='none';
        f.action='/search.html';
        f.method='get';
        f.innerHTML='<input type="hidden" name="keyword" value="'+keyword+'"/>';
        document.body.appendChild(f);
        f.submit();
    }
}

var HeaderAppConfig=[
    {num:0,title:'搜索关键词',input:$('#header_form_app .form_keyword'),error:$('#header_form_app .error-keyword'),hidden:''},
    {num:1,title:'搜索类型',input:$('#header_form_app .form_type')},
];
function search_app_go(){
    if(checkTitle(HeaderAppConfig[0]['input'],HeaderAppConfig[0]['error'],HeaderAppConfig[0]['title'])){
        var keyword = $.trim(HeaderAppConfig[0]['input'].val());
        var f=document.createElement('form');
        f.style.display='none';
        f.action='/search.html';
        f.method='get';
        f.innerHTML='<input type="hidden" name="keyword" value="'+keyword+'"/>';
        document.body.appendChild(f);
        f.submit();
    }
}

var BannerConfig=[
    {num:0,title:'搜索关键词',input:$('#index_banner_form .form_keyword'),error:$('#index_banner_form .error-keyword'),hidden:''},
];
function index_banner_form(){
    if(checkTitle(BannerConfig[0]['input'],BannerConfig[0]['error'],BannerConfig[0]['title'])){
        var keyword = $.trim(BannerConfig[0]['input'].val());
        var f=document.createElement('form');
        f.style.display='none';
        f.action='/search.html';
        f.method='get';
        f.innerHTML='<input type="hidden" name="keyword" value="'+keyword+'"/>';
        document.body.appendChild(f);
        f.submit();
    }
}


/* 添加cookie
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

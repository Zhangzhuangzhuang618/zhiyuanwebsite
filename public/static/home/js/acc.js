function pop_up(txt){
    $('.alert').html(txt).stop().fadeIn(500);
    setTimeout(function () {
        $(".alert").fadeOut(500);
    },2000)
  };

  function checknumber(value,msg) {
      //1.获取昵称
      
      var name = $.trim(value.val());
      //2.定义正则  itcast@163.com
      var reg_name =new RegExp("^[0-9-_]+$");
      //3.判断
      var flag = reg_name.test(name);
      if(name==''){
          pop_up(msg+'不能为空');
          value.addClass('input_focus');
          value.trigger( "select" );
      }else if(flag){
          value.removeClass('input_focus');
      }else{
       pop_up('只能输入数字和符号“-”，“_”');
          value.addClass('input_focus');
          value.trigger( "select" );
      }
      // 4.返回校验是否通过
      return flag;
    }
  //昵称验证
  function checkName(value,msg) {
      //1.获取昵称
      var name = $.trim(value.val());
      //2.定义正则  itcast@163.com
      var reg_name =new RegExp("^[A-Za-z0-9\u4e00-\u9fa5\-_]+$");
      //3.判断
      var flag = reg_name.test(name);
      if(name==''){
          pop_up(msg+'不能为空');
          value.addClass('input_focus');
          value.trigger( "select" );
      
      }else if(flag){
          value.removeClass('input_focus');
      }else{
       pop_up('只能输入中文、数字、英文和符号“-”，“_”');
          value.addClass('input_focus');
          value.trigger( "select" );
      }
      // 4.返回校验是否通过
      return flag;
    }
    
    //验证手机
    function checkTelephone(value) {
      //1.获取号码
      var phone = $.trim(value.val());
      //2.定义正则  itcast@163.com
      var reg_phone =/^1(3|4|5|6|7|8|9)\d{9}$/;
      //3.判断
      var flag = reg_phone.test(phone);
      if(phone==''){
          pop_up('手机号码不能为空');
          value.addClass('input_focus');
          value.trigger( "select" );
      }else if(flag){
          value.removeClass('input_focus');
      }else{
          pop_up('手机号码格式不正确');
          value.addClass('input_focus');
          value.trigger( "select" );
      }
      // 4.返回校验是否通过
      return flag;
    }
    function checkNull(value,msg) {
      var info = $.trim(value.val());
      if(info==''){
          pop_up(msg+'不能为空');
          value.addClass('input_focus');
          value.trigger( "select" );
          return false;
      }else{
          value.removeClass('input_focus');
          return true;}
    }
    function checkSelect(value,msg){
     
      val=$(value+" option:selected").val()
      if(val==''){
          pop_up(msg+'不能为空');
          return false;
      }else{
          return true;
      }
    }
    captcha.onclick=function(){
        this.src = "/verify";
    }
    
      // 校验邮箱
      function checkEmail(value) {
        //1.获取邮箱
        var email = $.trim(value.val());
        //2.定义正则  itcast@163.com
        //3.判断
     //    if(email!=''){
         var reg_email =new RegExp("^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$");
         var flag = reg_email.test(email);
            if(email==''){
                pop_up('邮箱不能为空');
                value.addClass('input_focus');
                value.trigger( "select" );
 
            }else if(flag){
                value.removeClass('input_focus');
            }else{
                pop_up('邮箱格式不正确');
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
     console.log(data)
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
    
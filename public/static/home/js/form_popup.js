$(document).ready(function(){
	//表单cookie
	function get_Cookie(cname){var name=cname+"=";var ca=document.cookie.split(';');for(var i=0;i<ca.length;i++){var c=ca[i];while(c.charAt(0)==' ')c=c.substring(1);if(c.indexOf(name)!=-1)return c.substring(name.length,c.length)}return""}
	// 页面表单
	$('#pageForm').submit(function(e) {
        var type = $('#form_banjiaType').val();
        var cityStart = $('#form_city_start').val();
        var cityEnd = $('#form_city_end').val();
        var phone = $('#form_mobile').val();
        console.log($(this).serialize())

        if (cityStart == '') {
            $("#form_city_start").focus();
            layer.tips("请选择起始地", '#form_city_start', {
                tips: [3, '#fe9901']
            });
            return false;
        }
        if (cityEnd == '') {
            $("#form_city_end").focus();
            layer.tips("请选择目的地", '#form_city_end', {
                tips: [3, '#fe9901']
            });
            return false;
        }
        if (phone == '' || !checkPhone(phone)) {
            $("#form_mobile").focus();
            layer.tips("请正确输入手机号", '#form_mobile', {
                tips: [3, '#fe9901']
            });
            return false;
        }
        var Lload = layer.load(1, { shade: [0.1, '#fff'] });
        $.ajax({
            cache: true,
            dataType: 'json',
            crossDomain: true,
            type: "POST",
            url: 'https://api.zhiranbj.com/admin/api/web_zixun/pass/zhiranbj',
            data: $(this).serialize() + "&form_source=pc_lianxi&page=" + replaceurl(location.href) +"&visitor_no="+get_Cookie("visitor_no")+"&siteid=54" + "&referrer=" + encodeURIComponent(replaceurl(encodeURI(encodeURI(document.referrer)))),
            async: true,
            error: function(XmlHttpRequest, textStatus, errorThrown) {
                alert(XmlHttpRequest.responseText);
            },
            success: function(data) {
                layer.close(Lload);

                if (data.status == 'true') {
                    window._agl && window._agl.push(['track', ['success', {t: 3}]]);
                    $('#pageForm')[0].reset();
                    layer.msg(data.msg, { icon: 1 });
                } else {
                    layer.msg(data.msg, { icon: 2 });
                }
            }
        });
        return false
    });
	//跨市搬家表单
	$("#form_kuashi").submit(function(){
		var start_city=$("#ks_start_city").val();	
		var end_city=$("#ks_end_city").val();	
		var mobile=$("#ks_mobile").val();
	    if(start_city=='')
		{
			$("#ks_start_city").focus();
			layer.tips("请选择起始城市", '#ks_start_city', {
				tips: [2, '#fe9901']
			});
			return false;
		}		 
	    if(end_city=='')
		{
			$("#ks_end_city").focus();
			layer.tips("请选择到达城市", '#ks_end_city', {
				tips: [2, '#fe9901']
			});
			return false;
		}
		if(mobile=='')
		{
			$("#ks_mobile").focus();
			layer.tips("请输入手机号码", '#ks_mobile', {
				tips: [2, '#fe9901']
			});
			return false;
		}
		else if(!checkPhone(mobile))
		{
			$("#ks_mobile").focus();
			layer.tips("请输入正确的手机号码", '#ks_mobile', {
				tips: [2, '#fe9901']
			});
			return false;
		}
		var Lload = layer.load(1, {shade: [0.1, '#fff']});
		$.ajax({
			cache: true,
			dataType:'json', 
			crossDomain: true,
			type: "POST",
			url:'https://api.zhiranbj.com/admin/api/web_zixun/pass/zhiranbj',
			data:$(this).serialize()+"&banjia_type=跨市搬家&source=pc_lianxi&page="+replaceurl(location.href)+"&visitor_no="+get_Cookie("visitor_no")+"&siteid=54" +"&referrer="+encodeURIComponent(replaceurl(encodeURI(encodeURI(document.referrer)))),
			async: true,
			error:function(XmlHttpRequest,textStatus, errorThrown){
				alert(XmlHttpRequest.responseText);
			},
			success: function(data) {
				layer.close(Lload);
				 if(data.status=='true'){
				     window._agl && window._agl.push(['track', ['success', {t: 3}]]);
					$('#form_kuashi')[0].reset();
					layer.msg(data.msg, {icon: 1});
				}else{
					layer.msg(data.msg, {icon: 2});
				}
			}
		});
		return false;
	});
	//同城搬家表单
	$("#form_tongcheng").submit(function(){
		var start_city=$("#tc_start_city").val();	
		var end_city=$("#tc_end_city").val();	
		var mobile=$("#tc_mobile").val();
	    if(start_city=='')
		{
			$("#tc_start_city").focus();
			layer.tips("请输入起始地址", '#tc_start_city', {
				tips: [2, '#fe9901']
			});
			return false;
		}		 
	    if(end_city=='')
		{
			$("#tc_end_city").focus();
			layer.tips("请输入到达地址", '#tc_end_city', {
				tips: [2, '#fe9901']
			});
			return false;
		}
		if(mobile=='')
		{
			$("#tc_mobile").focus();
			layer.tips("请输入手机号码", '#tc_mobile', {
				tips: [2, '#fe9901']
			});
			return false;
		}
		else if(!checkPhone(mobile))
		{
			$("#tc_mobile").focus();
			layer.tips("请输入正确的手机号码", '#tc_mobile', {
				tips: [2, '#fe9901']
			});
			return false;
		}
	
		return false;
	});

	// 头部咨询
	$("#top-head-form").submit(function(){
		var mobile=$("#top-mobile").val();
		if(mobile=='')
		{
			$("#top-mobile").focus();
			layer.tips("请输入手机号码", '#top-mobile', {
				tips: [2, '#fe9901']
			});
			return false;
		}
		else if(!checkPhone(mobile))
		{
			$("#top-mobile").focus();
			layer.tips("请输入正确的手机号码", '#top-mobile', {
				tips: [2, '#fe9901']
			});
			return false;
		}
		var Lload = layer.load(1, {shade: [0.1, '#fff']});
		$.ajax({
			cache: true,
			dataType:'json', 
			crossDomain: true,
			type: "POST",
			url:'https://api.zhiranbj.com/admin/api/web_zixun/pass/zhiranbj',
			data:$(this).serialize()+"&banjia_type=搬家咨询&source=pc_lianxi&page="+replaceurl(location.href)+"&visitor_no="+get_Cookie("visitor_no")+"&siteid=54" +"&referrer="+encodeURIComponent(replaceurl(encodeURI(encodeURI(document.referrer)))),
			async: true,
			error:function(XmlHttpRequest,textStatus, errorThrown){
				alert(XmlHttpRequest.responseText);
			},
			success: function(data) {
				layer.close(Lload);
				 if(data.status=='true'){
				     window._agl && window._agl.push(['track', ['success', {t: 3}]]);
					$('#top-head-form')[0].reset();
					layer.msg(data.msg, {icon: 1});
				}else{
					layer.msg(data.msg, {icon: 2});
				}
			}
		});
		return false;
	});
	
});
//检验手机号码是否正确
function checkPhone(phone){ 
    if(!(/^1[123456789]\d{9}$/.test(phone))){ 
        return false; 
    } 
	return true;
}
//规则替换
function replaceurl(url) 
{
    return url.replace(/\?/g, "**1**").replace(/\&/g, "**2**").replace(/\%/g, "**3**").replace(/\//g, "**4**");
}
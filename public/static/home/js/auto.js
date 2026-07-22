 function OnInput (obj) {
	bind_key($("#"+obj));
}
function bind_key(obj2)
{
	var obj=obj2;
	
	if(obj.siblings('.pos').length==0)
	{

		obj.parent().append("<span class=\"pos\"></span>")
	}
	
	var obj_index=obj.index();
	if(obj.val()=='')
        {
            $("#auto"+obj_index).remove();
            return false;
        }
		 //监听点击事件 
            $(document).click(function(e){ 
            e = window.event || e; // 兼容IE7
            var obj_22 = $(e.srcElement || e.target);
               if (!($(obj_22).is("#auto"+obj_index+" ul li")||obj.is(":focus"))) { 
                     $("#auto"+obj_index).remove();
                } 
           });
	// $.ajax({
	// 	cache: true,
	// 	dataType:'json', 
	// 	crossDomain: true,
	// 	type: "POST",
	// 	url:'https://api.yifeng.com/admin/ajax/ajaxPoiTips/keywords/'+obj.val(),
	// 	async: true,
	// 	error:function(XmlHttpRequest,textStatus, errorThrown){
	// 		alert(XmlHttpRequest.responseText);
	// 	},
	// 	success: function(data) {
	// 		 if(data.status=='true'){
	// 			  	$("#auto"+obj_index).remove();
	// 			if ( $(".auto"+obj_index).length <= 0 ) { 
	// 				    console.log(obj.parent().find(".pos").offset().left +"px")
	// 				    let objWidht = obj.parent().find(".pos").offset().left - 275
	// 					var div="<div class=\"autoSelector auto"+obj_index+"\" id=\"auto"+obj_index+"\" style=\"display:none;position: absolute;left: "+objWidht+"px; top: "+(obj.offset().top+obj.height()+3)+"px; z-index: 999999;\">";
	// 					div+="</ul></div>";
	// 					$(document.body).append(div);
	// 				} 
	// 				var param = data.param;
	// 				var ul="<ul class=\"autoslide mCustomScrollbar\" style=\"width:"+(obj.outerWidth())+"px;\">";
	// 				var i=0;
	// 				for (k in param) {
	// 					var _class="class=\"on\"";
	// 					 if(i!=0)
	// 					 {
	// 						 _class="";
	// 					 }
	// 					 if(param[k].address!=null&&param[k].district!=null)
	// 					 {
	// 						ul+="<li "+_class+" district=\""+param[k].district+"\" address=\""+param[k].address+"\" ><i></i><div><b class=\"autoname\">"+param[k].district+param[k].address+"</b></div></li>";
	// 					 }
	// 					i++;
	// 					if(i==6)
	// 					{
	// 						ul+="<li class=\"l_tips\"><b></b><b class=\"fr\">关闭</b></li>";
	// 						break;
	// 					}
	// 				}
	// 				ul+="</ul>";
	// 				if(i>0)
	// 				{
	// 					$("#auto"+obj_index).html(ul);
	// 					$("#auto"+obj_index).show();
	// 					$("#auto"+obj_index).on("click","li", function() {
	// 						 select($(this));
	// 					 });
	// 					 var cur=$("#auto"+obj_index+" li.on").index()==-1?0:$("#auto"+obj_index+" li.on").index();
	// 					 $(".Auto").keydown(function(event){                             
	// 						if(event.keyCode == 38){
	// 						   cur=cur-1==-1?i-1:cur-1;
	// 						   $("#auto"+obj_index+" li").removeClass("on");
	// 						   $("#auto"+obj_index+" li").eq(cur).addClass("on");
	// 						}else if (event.keyCode == 40){
	// 							cur=cur+1==i?0:cur+1;
	// 						   $("#auto"+obj_index+" li").removeClass("on");
	// 						   $("#auto"+obj_index+" li").eq(cur).addClass("on");
	// 						}else if (event.keyCode == 13){  
	// 							$("#auto"+obj_index+" li.on").click();
	// 						   return false;
	// 						}  
	// 					});  
	// 				}
	// 				function select(obj2)
	// 				{
	// 					if(obj2.find(".autoname").text()!='')
	// 					obj.val(obj2.find(".autoname").text());
	// 					$("#auto"+obj_index).remove();
	// 					$('.Auto').unbind("keydown");
	// 					return;
	// 				}
	// 		}else{
	// 			$("#auto"+obj_index).remove();
	// 			$('.Auto').unbind("keydown");
	// 		}
	// 	}
	// });
}
$(window).resize(function () { 
	$(".autoSelector").remove();				   
    $('.Auto').unbind("keydown");
	
});
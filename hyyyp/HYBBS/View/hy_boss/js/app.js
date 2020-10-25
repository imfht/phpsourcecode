//依赖JQuery
//
// 
function setCookie(name,value,hours){  
    var d = new Date();
    d.setTime(d.getTime() + hours * 3600 * 1000);
    document.cookie = name + '=' + value + '; expires=' + d.toGMTString();
}
function getCookie(name){  
    var arr = document.cookie.split('; ');
    for(var i = 0; i < arr.length; i++){
        var temp = arr[i].split('=');
        if(temp[0] == name){
            return temp[1];
        }
    }
    return '';
}
function removeCookie(name){
    var d = new Date();
    d.setTime(d.getTime() - 10000);
    document.cookie = name + '=1; expires=' + d.toGMTString();
}

$(document).ready( function(){
	if(getCookie("WIDTH_4") == "1"){
        console.log('test');
		$(".container").css("width","auto");
	}
	$("#setWidth").click(function(){
		if(getCookie("WIDTH_4") != "1"){
			setCookie("WIDTH_4","1");
			$(this).attr("class","icon-shrink2");
			$(".container").css("width","auto");
		}else{

			removeCookie("WIDTH_4");
			$(this).attr("class","icon-enlarge2");
			$(".container").css("width","");
		}
	})

	
	$(".js-info").each(function(){
		var _this = this;
		var pos = 'south';
		var attr = $(this).attr('pos');

		if(attr=='left')
			pos = 'east';
		else if(attr == 'right')
			pos = 'west';
		else if(attr == 'bottom')
			pos = 'north'
		$(_this).darkTooltip({
			size:'lg',
			gravity:pos,
			content:'<img src="'+www+'View/hy_boss/loading.gif">',
			animation:'flipIn',
			ajax:www+'ajax'+exp+'userjson',
			ajaxdata:{
				uid:$(_this).attr('uid')
			}
		});
	})

});
function friend(uid,obj){
    friend_state(uid,function(b,m){
        var _obj = $(obj);
        if(m){
            _obj.removeClass("bg-primary");
            _obj.addClass("bg-red");
            _obj.text("取消关注");
        }
        else{
            _obj.removeClass("bg-red");
            _obj.addClass("bg-primary");
            _obj.text("关注");
        }
    })
}
function clear_mess(){
	swal({
	    title: "清空未读数量",
	    text: "将会清空你的未读消息数量.不会清空聊天记录",
	    type: "warning",
	    showCancelButton: true,
	     confirmButtonColor: "#DD6B55",
	     confirmButtonText: "删除",
	     cancelButtonText:'取消'
	 }, function(){
	     $.ajax({
	         url: www+'ajax'+exp+"clear_mess",
	         type:"POST",
	         cache: false,
	         dataType: 'json'
	     }).then(function(e) {
	         setTimeout(function(){
	             swal(e.error?"操作成功":"操作失败", e.info, e.error?"success": "error");
	         },100);
	         $(".xx").text('').hide();

	         

	     }, function() {
	         swal("失败", "请尝试重新提交", "error");
	     });
	 });
}
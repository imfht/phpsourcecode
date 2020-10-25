$(document).ready(function(){
//首先将#back-to-top隐藏
$("#back-to-top").hide();
//当滚动条的位置处于距顶部100像素以下时，跳转链接出现，否则消失
$(function () {
	$(window).scroll(function(){
	if ($(window).scrollTop()>100){
	$("#back-to-top").fadeIn(500);
	}
	else
	{
	$("#back-to-top").fadeOut(500);
	}
	});
	//当点击跳转链接后，回到页面顶部位置
	$("#back-to-top").click(function(){
	$('body,html').animate({scrollTop:0},100);
	return false;
	});
	});
	});
$(function(){
	
	var winWidth = parseInt($(window).width());
	
	$('#banner img.img').each(function(){
		
		$(this).height(winWidth * 360 / 1200);
		
	});
	
});
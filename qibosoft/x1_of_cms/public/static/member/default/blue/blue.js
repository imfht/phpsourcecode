$(function(){
	$(".FormTable tr").mouseover(function(){
		$(".FormTable tr").removeClass("over");
		$(this).addClass("over");
	});	 
	$(".FormTable tr").mouseout(function(){
		$(".FormTable tr").removeClass("over");
	});	
	$(".ListTable tr").mouseover(function(){
		$(".ListTable tr").removeClass("over");
		$(this).addClass("over");
	});	
	$(".ListTable tr").mouseout(function(){
		$(".ListTable tr").removeClass("over");
	});	
	$('input:submit').addClass("submits");
	$('input:reset').addClass("resets");
	$('input:button').addClass("resets");
});
$(function(){
	var clr = null;
	var page=1;
	$("#imagePlay ul li:not(:first)").hide();
	clr = setInterval(move,2000);
	
	$("#spanPlay span").mouseover(function(){
		clearInterval(clr);
		$(this).addClass("on").siblings().removeClass("on");
		var adr = $(this).index();
		$("#imagePlay ul li").hide(0);
		$("#imagePlay ul li:eq("+adr+")").fadeIn(0);
	});
	
	$("#spanPlay span").mouseout(function(){
		clearInterval(clr);
		page = $(this).index()+1;
		clr = setInterval(move,2000);
	});
	
	function move()
	{
		if($("#imagePlay ul li:last").is(":visible"))
		{
			page=0;
			$("#spanPlay").children("span:eq("+page+")").addClass("on")
			.siblings().removeClass("on");
			$("#imagePlay ul li:not(:first)").hide();
			$("#imagePlay ul li:first").fadeIn(200);	
			page++;			
		}
		else
		{
			$("#imagePlay ul li:visible").next().show();
			$("#spanPlay").children("span:eq("+page+")").addClass("on")
			.siblings().removeClass("on");
			page++;
		}
	}
})
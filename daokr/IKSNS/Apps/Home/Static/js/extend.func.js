function selectTheme(obj,theme){
	$(obj).addClass('on').siblings().removeClass();
	
	$('#ikTheme').attr('href',siteUrl+'Public/Theme/'+theme+'/base.css');
	var date = new Date();
    date.setTime(date.getTime()+(30*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
	document.cookie = "ikTheme="+theme+expires+";path=/";
}

$(function(){
	var sWidth = $("#head-slide-box").width(); //获取焦点图的宽度（显示面积）
	var len = $("#head-slide-box li.item").length; //获取焦点图个数
	var index = 0;
	var picTimer;

	//为小按钮添加鼠标滑入事件，以显示相应的内容
	$(".head-ctrls li").mouseenter(function() {
		index = $(".head-ctrls li").index(this);
		showPics(index);
	}).eq(0).trigger("mouseenter");
	
	//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
	$(".head-slides").css("width",sWidth * (len));
	$(".head-slides").find('.item').show();

	//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
	$("#head-slide-box").hover(function() {
		clearInterval(picTimer);
	},function() {
		picTimer = setInterval(function() {
			showPics(index);
			index++;
			if(index == len) {index = 0;}
		},6000); //此4000代表自动播放的间隔，单位：毫秒
	}).trigger("mouseleave");
		
	//显示图片函数，根据接收的index值显示相应的内容
	function showPics(index) { //普通切换
		var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
		$(".head-slides").stop(true,false).animate({"margin-Left":nowLeft},800); //通过animate()调整ul元素滚动到计算出的position
		$(".head-ctrls li").eq(index).addClass('on').siblings().removeClass('on');	
	}	
	
});


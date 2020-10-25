// JavaScript Document
(function($){
	var goToTopTime;
	$.fn.goToTop=function(options){
		var opts = $.extend({},$.fn.goToTop.def,options);
		var $window=$(window);
		$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body'); // opera fix
		//$(this).hide();
		var $this=$(this);
		clearTimeout(goToTopTime);
		goToTopTime=setTimeout(function(){
			var controlLeft;
			if ($window.width() > opts.pageHeightJg * 2 + opts.pageWidth) {
				controlLeft = ($window.width() - opts.pageWidth) / 2 + opts.pageWidth + opts.pageWidthJg;
			}else{
				controlLeft = $window.width()- opts.pageWidthJg-$this.width();
			}
			var cssfixedsupport=$.support.leadingWhitespace;//判断是否ie6
			var controlTop=$window.height() - $this.height()-opts.pageHeightJg;
			controlTop=cssfixedsupport ? $window.scrollTop() + controlTop : controlTop;
			var shouldvisible=( $window.scrollTop() >= opts.startline )? true : false;
			
			if (shouldvisible){
				$this.stop().show();
			}else{
				$this.stop().hide();
			}
			
			$this.css({
				position: cssfixedsupport ? 'absolute' : 'fixed',
				top: controlTop,
				left: controlLeft
			});
		},30);
		
		$(this).click(function(event){
			$body.stop().animate( { scrollTop: $(opts.targetObg).offset().top}, opts.duration);
			$(this).blur();
			event.preventDefault();
			event.stopPropagation();
		});
	};
	
	$.fn.goToTop.def={
		pageWidth:1170,//页面宽度
		pageWidthJg:16,//按钮和页面的间隔距离
		pageHeightJg:100,//按钮和页面底部的间隔距离
		startline:30,//出现回到顶部按钮的滚动条scrollTop距离
		duration:200,//回到顶部的速度时间
		targetObg:"body"//目标位置
	};
})(jQuery);
$(function(){
	$('<a href="javascript:void(0);" class="backToTop" title="返回顶部" style="text-decoration:none;">&nbsp;</a>').appendTo("body");
});
$(function(){
	$(".backToTop").goToTop({pageWidth:$('body>.container').width()});
	$(window).bind('scroll resize',function(){
		$(".backToTop").goToTop({pageWidth:$('body>.container').width()});
	});
});
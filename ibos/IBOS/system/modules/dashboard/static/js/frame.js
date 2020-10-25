/**
 * frame.js
 * 后台框架
 * @module		Dashboard
 * @submodule 	frame
 * @modified	2013-04-23
 * @version		$Id$
 * @author 		Inaki
 */

/**
 * Framework
 */
(function(){
	var adjustMainerHeight = function(){
		var height = (window.innerHeight||document.documentElement.clientHeight) - 50;
		Dom.byId("mainer").style.height = height + "px"
	}
	adjustMainerHeight();
	$(window).resize(adjustMainerHeight)

	/**
	 * 调整布局宽高以适应屏幕
	 * @method adjustLayout
	 * @todo 改进，避免页面跳动
	 */
	// var adjustLayout = function () {
	// 	var size = Layout.getValidSize();
	// 	var logo = Dom.byId("logo"),
	// 		bar = Dom.byId("bar"),
	// 		aside = Dom.byId("aside"),
	// 		mc = Dom.byId("mc");
	// 	// aside.style.height = mc.style.height = size.height - 50 + "px";
	// 	if(size.width * 0.2 > 230){
	// 		aside.style.width = logo.style.width = bar.style.marginLeft = mc.style.marginLeft = size.width * 0.2 + "px";
	// 	}else{
	// 		aside.style.width = logo.style.width = bar.style.marginLeft = mc.style.marginLeft = "230px";
	// 	}
	// }

	// window.onload = window.onresize = adjustLayout;

	function toggleNav(){
		var $el = $(this)
			//当前活动元素为a标签时，active加在其父节点li上
		var $item = $el.is("a") ? $el.parent() : $el
		var $subNav = $($el.data("href"));
		if ($item.hasClass("active")){
			$subNav.toggle()
			$subNav.find('.sub-link').eq(0).trigger('click')
		} else {
			$item
				.siblings()
				.children('.sub-nav')
				.hide()
			$subNav.show().find('.sub-link').eq(0).trigger('click')
		}
	}

	function toggleActive () {
		$(this).parent().addClass('active').siblings().removeClass('active')
	}

	$('.main-link').on('click', toggleNav);
	$('.main-link').on('click', toggleActive)
	$('.sub-link').on('click', toggleActive)
})()

var leftMenuList = null;
function sliderResize() {
	var menuTitleWidth = 0;
	if ($('#page_left_slider').hasClass('closed')) {
		menuTitleWidth = 0;
	} else {
		menuTitleWidth = 180;
	}

	var borderAdjust = 49;
	if (navigator.userAgent.indexOf('Firefox') >= 0
		|| navigator.userAgent.indexOf('Chrome') >= 0) {
		borderAdjust = 0;
	}

	$('#page_left_slider').animate({'width': (40 + menuTitleWidth)}, 300, 'swing');
	$('#page_content').animate({'width': ($(window).width() - (40 + menuTitleWidth) - 1 - borderAdjust)}, 300, 'swing', function () {
		$('#left_slider_shrink span:first-child').text(menuTitleWidth ? '<<' : '>>');

		if (menuTitleWidth == 0) {
			leftMenuList.smallLayout();
		}
	});
}

function pageMainResize() {
	$('#page_main').height($(window).height() - $('#page_top_bar').height());
	sliderResize();
}
$(function () {
	var topMenuList = $("#top_menu_container").topMenu({'menu_container': '#top_menu_container'});
	leftMenuList = $("#left_menu_container").leftMenu({'menu_container': '#left_menu_container'});

	pageMainResize();
	window.onresize = function () {
		pageMainResize();
	};
	$('#left_slider_shrink').on('click', function () {
		$('#page_left_slider').toggleClass('closed');
		sliderResize();
		return false;
	});
});
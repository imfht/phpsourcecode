/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

$(function(){

	if (typeof(homeslider_speed) == 'undefined')
		homeslider_speed = 500;
	if (typeof(homeslider_pause) == 'undefined')
		homeslider_pause = 3000;
	if (typeof(homeslider_loop) == 'undefined')
		homeslider_loop = true;

	$('#homeslider').bxSlider({
		infiniteLoop: homeslider_loop,
		hideControlOnEnd: true,
		pager: true,
		autoHover: true,
		auto: homeslider_loop,
		speed: homeslider_speed,
		pause: homeslider_pause,
		controls: false
	});
});
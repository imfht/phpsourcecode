/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function pc_serialScrollFixLock(event, targeted, scrolled, items, position)
{
	var leftArrow = position == 0 ? true : false;
	var rightArrow = position + 5 >= $('#productscategory_list li:visible').length ? true : false;
	
	$('a#productscategory_scroll_left').css('cursor', leftArrow ? 'default' : 'pointer').fadeTo(0, leftArrow ? 0 : 1);		
	$('a#productscategory_scroll_right').css('cursor', rightArrow ? 'default' : 'pointer').fadeTo(0, rightArrow ? 0 : 1).css('display', rightArrow ? 'none' : 'block');

	return true;
}

$(document).ready(function()
{
	$('#productscategory_list').serialScroll({
		items: 'li',
		prev: 'a#productscategory_scroll_left',
		next: 'a#productscategory_scroll_right',
		axis: 'x',
		offset: 0,
		stop: true,
		onBefore: pc_serialScrollFixLock,
		duration: 300,
		step: 1,
		lazy: true,
		lock: false,
		force: false,
		cycle: false });
	$('#productscategory_list').trigger( 'goto', 0);
});
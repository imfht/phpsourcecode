/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function toggleLayer(whichLayer, flag)
{
	if (!flag)
		$(whichLayer).hide();
	else
		$(whichLayer).show();
}

function openCloseLayer(whichLayer, action)
{
	if (!action)
	{
		if ($(whichLayer).css('display') == 'none')
			$(whichLayer).show();
		else
			$(whichLayer).hide();
	}
	else if (action == 'open')
		$(whichLayer).show();
	else if (action == 'close')
		$(whichLayer).hide();
}
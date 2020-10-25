{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}


{if isset($module_content)}
	{$module_content}
{else}
	{if !isset($smarty.get.configure)}
		{include file='controllers/modules/js.tpl'}
		{if isset($smarty.get.select) && $smarty.get.select eq 'favorites'}
			{include file='controllers/modules/favorites.tpl'}
		{else}
			{include file='controllers/modules/page.tpl'}
		{/if}
	{/if}
{/if}

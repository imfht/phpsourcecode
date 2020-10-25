{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/list/list_content.tpl"}

{block name="td_content"}
	{if $key == 'url'}
		<a href="{$tr.$key}" onmouseover="$(this).css('text-decoration', 'underline')" onmouseout="$(this).css('text-decoration', 'none')" target="_blank">{$tr.$key}</a>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/options/options.tpl"}
{block name="input"}
	{if $field['type'] == 'disabled'}
		{$field['disabled']}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

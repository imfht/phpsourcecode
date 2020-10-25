{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/form/form.tpl"}

{block name="input"}
	{if $input.type == 'select_category'}
		<select name="id_parent">
			{$input.options.html}
		</select>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}


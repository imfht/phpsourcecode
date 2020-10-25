{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{extends file="helpers/form/form.tpl"}

{block name="field"}
	{$smarty.block.parent}
	{if $input.name == 'name'}
		{hook h="displayFeatureForm" id_feature=$form_id}
	{/if}
{/block}

{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{extends file="helpers/form/form.tpl"}

{block name=script}
	// At the loading
	($("input[name='is_free']:checked").val() == 0) ? $('#shipping_costs_div').show('toggle'): $('#shipping_costs_div').hide();

	$("input[name='is_free']").live('change', function() {ldelim}
		($("input[name='is_free']:checked").val() == 0) ? $('#shipping_costs_div').show('toggle'): $('#shipping_costs_div').hide();			
	{rdelim});
{/block}

{block name="label"}
	{if $input.type == 'select' && $input.name == 'id_tax_rules_group'}
		<div id="shipping_costs_div" style="display:{if isset($fields_value.is_free) && $fields_value.is_free}none{else}block{/if}">
	{/if}
	{$smarty.block.parent}
{/block}

{block name="field"}
	{$smarty.block.parent}
	{if $input.type == 'select' && $input.name == 'range_behavior'}
		</div>
	{/if}
{/block}

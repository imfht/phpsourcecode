{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{extends file="helpers/list/list_header.tpl"}
{block name=override_header}
	{if count($list_warehouses) > 0}
<div class="filter-stock">
		<form type="get">
			<label for="id_warehouse">{l s='Filter movements by warehouse:'}</label>
			<input type="hidden" name="controller" value="AdminStockMvt" />
			<input type="hidden" name="token" value="{$token}" />
			<select name="id_warehouse" onChange="$(this).parent().submit();">
				{foreach $list_warehouses as $warehouse}
					<option {if $warehouse.id_warehouse == $current_warehouse}selected="selected"{/if} value="{$warehouse.id_warehouse}">{$warehouse.name}</option>
				{/foreach}
			</select>
		</form>
	{/if}
</div>
{/block}

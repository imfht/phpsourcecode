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
{if isset($warehouses) && count($warehouses) > 0 && isset($filter_status)}
<div class="filter-stock-extended">
	<form id="supply_orders" type="get">
		<input type="hidden" name="controller" value="AdminSupplyOrders" />
		<input type="hidden" name="token" value="{$token}" />
		<div>
			<label for="id_warehouse">{l s='Filter by warehouse:'}</label>
			<select name="id_warehouse" onChange="$(this).parent().parent().submit();">
			{foreach from=$warehouses key=k item=i}
				<option {if $i.id_warehouse == $current_warehouse} selected="selected"{/if} value="{$i.id_warehouse}">{$i.name}</option>
			{/foreach}
			</select>
		</div>
		<div style="margin-top: 5px;">
			<label for="filter_status">{l s='Choose not to display completed/canceled orders:'}</label>
			<input type="checkbox" name="filter_status" class="noborder" onChange="$(this).parent().parent().submit();" {if $filter_status == 1}value="on" checked{/if}></input>
		</div>
	</form>
</div>
{/if}
{/block}


		
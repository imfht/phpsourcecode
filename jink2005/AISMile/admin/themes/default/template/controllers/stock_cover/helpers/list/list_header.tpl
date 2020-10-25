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
<div class="filter-stock-extended">
	<form id="stock_cover" type="get">
		<input type="hidden" name="controller" value="AdminStockCover" />
		<input type="hidden" name="token" value="{$token}" />
	{if count($stock_cover_periods) > 1}
	<div>
			<label for="coverage_period">{l s='Filter by period:'}</label>
			<select name="coverage_period" onChange="$(this).parent().parent().submit();">
				{foreach from=$stock_cover_periods key=k item=i}
					<option {if $i == $stock_cover_cur_period} selected="selected"{/if} value="{$i}">{$k}</option>
				{/foreach}
			</select>
	</div>
	{/if}
	{if count($stock_cover_warehouses) > 0}
	<div>
			<label for="id_warehouse">{l s='Filter by warehouse:'}</label>
			<select name="id_warehouse" onChange="$(this).parent().parent().submit();"">
				{foreach from=$stock_cover_warehouses key=k item=i}
					<option {if $i.id_warehouse == $stock_cover_cur_warehouse} selected="selected"{/if} value="{$i.id_warehouse}">{$i.name}</option>
				{/foreach}
			</select>
	</div>
	{/if}
	<div>
		<label for="warn_days">{l s='Highlight when coverage (in days) is less than:'}</label>
		<input name="warn_days" type="text" size="3" onChange="$(this).parent().parent().submit();" 
			   value="{if isset($stock_cover_warn_days)}{$stock_cover_warn_days}{/if}">
		</input>
	</div>
	</form>
</div>
{/block}

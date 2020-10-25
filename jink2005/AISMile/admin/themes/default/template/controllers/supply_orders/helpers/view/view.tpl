{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
	<div style="margin-top: 20px;">
		<fieldset>
			<legend>{if isset($is_template) && $is_template == 1} {l s='Template'} {/if}{l s='General information'}</legend>
			<table style="width: 400px;" classe="table">
				<tr>
					<td>{l s='Creation date:'}</td>
					<td>{$supply_order_creation_date}</td>
				</tr>
				<tr>
					<td>{l s='Supplier:'}</td>
					<td>{$supply_order_supplier_name}</td>
				</tr>
				<tr>
					<td>{l s='Last update:'}</td>
					<td>{$supply_order_last_update}</td>
				</tr>
				<tr>
					<td>{l s='Delivery expected:'}</td>
					<td>{$supply_order_expected}</td>
				</tr>
				<tr>
					<td>{l s='Warehouse:'}</td>
					<td>{$supply_order_warehouse}</td>
				</tr>
				<tr>
					<td>{l s='Currency:'}</td>
					<td>{$supply_order_currency->name}</td>
				</tr>
				<tr>
					<td>{l s='Global discount rate:'}</td>
					<td>{$supply_order_discount_rate} %</td>
				</tr>
			</table>
		</fieldset>
	</div>

	<div style="margin-top: 20px;">
		<fieldset>
			<legend>{if isset($is_template) && $is_template == 1} {l s='Template'} {/if}{l s='Products'}</legend>
			{$supply_order_detail_content}
		</fieldset>
	</div>

	<div style="margin-top: 20px;">
		<fieldset>
			<legend>{if isset($is_template) && $is_template == 1} {l s='Template'} {/if}{l s='Summary'}</legend>
			<table style="width: 400px;" classe="table">
				<tr>
					<th>{l s='Designation'}</th>
					<th width="100px">{l s='Value'}</th>
				</tr>
				<tr>
					<td bgcolor="#000000"></td>
					<td bgcolor="#000000"></td>
				</tr>
				<tr>
					<td>{l s='Total (tax excl.)'}</td>
					<td align="right">{$supply_order_total_te}</td>
				</tr>
				<tr>
					<td>{l s='Discount'}</td>
					<td align="right">{$supply_order_discount_value_te}</td>
				</tr>
				<tr>
					<td>{l s='Total with discount (tax excl.)'}</td>
					<td align="right">{$supply_order_total_with_discount_te}</td>
				</tr>
				<tr>
					<td bgcolor="#000000"></td>
					<td bgcolor="#000000"></td>
				</tr>
				<tr>
					<td>{l s='Total Tax'}</td>
					<td align="right">{$supply_order_total_tax}</td>
				</tr>
				<tr>
					<td>{l s='Total (tax incl.)'}</td>
					<td align="right">{$supply_order_total_ti}</td>
				</tr>
				<tr>
					<td bgcolor="#000000"></td>
					<td bgcolor="#000000"></td>
				</tr>
				<tr>
					<td>{l s='TOTAL TO PAY'}</td>
					<td align="right">{$supply_order_total_ti}</td>
				</tr>
			</table>
		</fieldset>
	</div>

{/block}

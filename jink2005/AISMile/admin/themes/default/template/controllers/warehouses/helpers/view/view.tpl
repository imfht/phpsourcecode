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
{if isset($warehouse)}
	<div>
			<fieldset>
				<legend><img src="../img/t/AdminPreferences.gif" alt="" />  {l s='General informations'}</legend>
				<table style="width: 400px;" classe="table">
					<tr>
						<td>{l s='Reference:'}</td>
						<td>{$warehouse->reference}</td>
					</tr>
					<tr>
						<td>{l s='Name:'}</td>
						<td>{$warehouse->name}</td>
					</tr>
					<tr>
						<td>{l s='Manager:'}</td>
						<td>{$employee->lastname}{$employee->firstname}</td>
					</tr>
					<tr>
						<td>{l s='Country:'}</td>
						<td>{if $address->country != ''}{$address->country}{else}{l s='N/D'}{/if}</td>
					</tr>
					<tr>
						<td>{l s='Phone:'}</td>
						<td>{if $address->phone != ''}{$address->phone}{else}{l s='N/D'}{/if}</td>
					</tr>
					<tr>
						<td>{l s='Management type:'}</td>
						<td>{l s=$warehouse->management_type}</td>
					</tr>
					<tr>
						<td>{l s='Valuation currency:'}</td>
						<td>{$currency->name} ({$currency->sign})</td>
					</tr>
					<tr>
						<td>{l s='Products:'}</td>
						<td>{$warehouse_num_products} {l s='References:'}</td>
					</tr>
					<tr>
						<td>{l s='Physical product quantities:'}</td>
						<td>{$warehouse_quantities}</td>
					</tr>
					<tr>
						<td>{l s='Stock valuation:'}</td>
						<td>{$warehouse_value}</td>
					</tr>
				</table>
			</fieldset>
		</div>
		<div style="margin-top: 30px">
			<fieldset>
				<legend><img src="../img/t/AdminShop.gif" alt="" /> {l s='Shops'}</legend>
				{l s='The following are the shops associated with this warehouse.'}
				<table style="width: 400px; margin-top:20px" classe="table">
					<tr>
						<th>{l s='ID'}</th>
						<th>{l s='Name'}</th>
					{foreach $shops as $shop}
					<tr>
						<td>{$shop.id_shop}</td>
						<td>{$shop.name}</td>
					</tr>
					{/foreach}
				</table>
			</fieldset>
		</div>
		<div style="margin-top: 30px">
			<fieldset>
				<legend><img src="../img/t/AdminStock.gif" alt="" /> {l s='Stock'}</legend>
				<a href="index.php?controller=adminstockinstantstate&id_warehouse={$warehouse->id}&token={getAdminToken tab='AdminStockInstantState'}">{l s='Click here if you want details on products in this warehouse'}</a>
			</fieldset>
		</div>
		<div style="margin-top: 30px">
		<fieldset>
			<legend><img src="../img/t/AdminLogs.gif" alt="" /> {l s='History'}</legend>
			<a href="index.php?controller=adminstockmvt&id_warehouse={$warehouse->id}&token={getAdminToken tab='AdminStockMvt'}">{l s='Click here if you want details on what happened in this warehouse'}</a>
		</fieldset>
		</div>
{else}
	{l s='This warehouse does not exist'}
{/if}
{/block}

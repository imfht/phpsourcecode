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

	<fieldset>
		<ul>
			<li><span style="font-weight: bold; font-size: 13px; color:#000;">{l s='Name:'}</span> {$group->name[$language->id]}</li>
		<li><span style="font-weight: bold; font-size: 13px; color:#000;">{l s='Discount: %d%%' sprintf=$group->reduction}</span></li>
		<li><span style="font-weight: bold; font-size: 13px; color:#000;">{l s='Current category discount:'}</span>
			{if !$categorieReductions}
				{l s='None'}
			{else}
				<table cellspacing="0" cellpadding="0" class="table" style="margin-top:10px">
					{foreach $categorieReductions key=key item=category }
						<tr class="alt_row">
							<td>{$category.path}</td>
							<td>{l s='Discount: %d%%' sprintf=$category.reduction}</td>
						</tr>
					{/foreach}
				</table>
			{/if}
			</li>
			
		<li><span style="font-weight: bold; font-size: 13px; color:#000;">{l s='Price display method:'}</span>
			{if $group->price_display_method}
				{l s='Tax excluded'}
			{else}
				{l s='Tax included'}
			{/if}
		</li>
		<li><span style="font-weight: bold; font-size: 13px; color:#000;">{l s='Show prices:'}</span> {if $group->show_prices}{l s='Yes'}{else}{l s='No'}{/if}
		</li>
		</ul>
	</fieldset>
	<h2>{l s='Members of this customer group'}</h2>
	{$customerList}

{/block}
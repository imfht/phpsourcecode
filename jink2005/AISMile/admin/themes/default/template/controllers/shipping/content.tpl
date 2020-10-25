{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{$content}
<br /><br />
<h2>{l s='Fees by carrier, geographical zone, and ranges'}</h2>
<form action="{$action_fees}" id="fees" name="fees" method="post">
	<fieldset>
		<legend><img src="../img/admin/delivery.gif" />{l s='Fees'}</legend>
		{if empty($carriers)}
			{l s='You only have free carriers, there is no need to configure your delivery prices.'}
		{else}
			<b>{l s='Carrier:'} </b>
			<select name="id_carrier2" onchange="$('#fees').attr('action', $('#fees').attr('action')+'&id_carrier='+$(this).attr('value')+'#fees'); $('#fees').submit();">
				{foreach $carriers AS $carrier}
					<option value="{$carrier['id_carrier']|intval}" {if $carrier['id_carrier'] == $id_carrier} selected="selected"{/if}>{$carrier['name']}</option>
				{/foreach}
			</select><br />

			<table class="table space" cellpadding="0" cellspacing="0">
				<tr>
					<th>{l s='Zone / Range'}</th>
					{if !$carrierSelected->is_free}
						{foreach $ranges AS $range}
							<th style="font-size: 11px;">{$range['delimiter1']|floatval}{$suffix} {l s='to'} {$range['delimiter2']|floatval}{$suffix}</th>
						{/foreach}
					{/if}
				</tr>
				{if sizeof($ranges) && !$carrierSelected->is_free}
					{if sizeof($zones) > 1}
						<tr>
							<th style="height: 30px;">{l s='All'}</th>
							{foreach $ranges AS $range}
								<td class="center">
									{$currency->getSign('left')}
									<input type="text" id="fees_all_{$range[$rangeIdentifier]}" onchange="this.value = this.value.replace(/,/g, \'.\');" onkeyup="if ((event.keyCode||event.which) != 9){ spreadFees({$range[$rangeIdentifier]})}" style="width: 45px;" />
									{$currency->getSign('right')} {l s='(tax excl.)'}
								</td>
							{/foreach}
						</tr>
					{/if}
		
					{foreach $zones AS $zone}
						<tr>
							<th style="height: 30px;">{$zone['name']}</th>
							{foreach $ranges AS $range}
								{if isset($deliveryArray[$zone['id_zone']][$id_carrier][$range[$rangeIdentifier]])}
									{$price = $deliveryArray[$zone['id_zone']][$id_carrier][$range[$rangeIdentifier]]}
								{else}
									{$price = '0.00'}
								{/if}
								<td class="center">
									{$currency->getSign('left')}
									<input 
										type="text" 
										class="fees_{$range[$rangeIdentifier]}" 
										onchange="this.value = this.value.replace(/,/g, \'.\');" name="fees_{$zone['id_zone']}_{$range[$rangeIdentifier]}" onkeyup="clearAllFees({$range[$rangeIdentifier]})" 
										value="{$price|string_format:"%.2f"}"
										style="width: 45px;" 
									/>
									{$currency->getSign('right')} {l s='(tax excl.)'}
								</td>
							{/foreach}
						</tr>
					{/foreach}
				{/if}
				<tr>
					<td colspan="{$ranges|sizeof + 1}" class="center" style="border-bottom: none; height: 40px;">
						<input type="hidden" name="submitFees{$table}" value="1" />
					{if sizeof($ranges) && !$carrierSelected->is_free}
						<input type="submit" value="{l s='   Save   '}" class="button" />
					{else if $carrierSelected->is_free}
						{l s='This is a free carrier'}
					{else}
						{l s='No ranges set for this carrier'}
					{/if}
					</td>
				</tr>
			</table>
		{/if}
		<input type="hidden" name="id_carrier" value="{$id_carrier}" />
	</fieldset>
</form>

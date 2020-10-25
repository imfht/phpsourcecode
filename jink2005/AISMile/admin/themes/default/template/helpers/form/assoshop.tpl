{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<script type="text/javascript">
$().ready(function() {
	$('.input_all_shop').live('click', function() {
		var checked = $(this).prop('checked');
		$('.input_shop_group:not(:disabled)').attr('checked', checked);
		$('.input_shop:not(:disabled)').attr('checked', checked);
	});

	// Click on a group shop
	$('.input_shop_group').live('click', function() {
		$('.input_shop[value='+$(this).val()+']').attr('checked', $(this).prop('checked'));
		check_all_shop();
	});

	// Click on a shop
	$('.input_shop').live('click', function() {
		check_shop_group_status($(this).val());
		check_all_shop();
	});

	// Initialize checkbox
	$('.input_shop_group').each(function(k, v) {
		check_shop_group_status($(v).val());
		check_all_shop();
	});
});

function check_shop_group_status(id_group) {
	var groupChecked = true;
	var total = 0;
	$('.input_shop[value='+id_group+']').each(function(k, v) {
		total++;
		if (!$(v).prop('checked'))
			groupChecked = false;
	});

	if (total > 0)
		$('.input_shop_group[value='+id_group+']').attr('checked', groupChecked);
}

function check_all_shop() {
	var allChecked = true;
	$('.input_shop_group:not(:disabled)').each(function(k, v) {
		if (!$(v).prop('checked'))
			allChecked = false;
		});
	$('.input_all_shop').attr('checked', allChecked);
}
</script>

<div class="assoShop">
	<table class="table" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<th>{l s='Shop'}</th>
		</tr>
		<tr>
			<td>
				<label class="t"><input class="input_all_shop" type="checkbox" /> <b>{l s='All shops'}</b></label>
			</td>
		</tr>
		{foreach $input.values as $groupID => $groupData}
				{assign var=groupChecked value=false}
			<tr {if $input.type == 'shop'}class="alt_row"{/if}>
				<td>
					<img style="vertical-align:middle;" alt="" src="../img/admin/lv2_b.gif" />
					<label class="t">
						<input class="input_shop_group"
							type="checkbox"
							name="checkBoxShopGroupAsso_{$table}[{$groupID}]"
							value="{$groupID}"
							{if $groupChecked} checked="checked"{/if} />
						<b>{l s='Group:'} {$groupData['name']}</b>
					</label>
				</td>
			</tr>
	
			{if $input.type == 'shop'}
				{assign var=j value=0}
				{foreach $groupData['shops'] as $shopID => $shopData}
					{if (isset($fields_value.shop[$shopID]))}
						{assign var=checked value=true}
					{else}
						{assign var=checked value=false}
					{/if}
					<tr>
						<td {if $groupData['disable_shops']}style="font-style:italic;background-color:#CFC4FF"{/if}>
							<img style="vertical-align:middle;" alt="" src="../img/admin/lv3_{if $j < count($groupData['shops']) - 1}b{else}f{/if}.png" />
							<label class="child">
								<input class="input_shop"
									type="checkbox"
									value="{$groupID}"
									shop_id="{$shopID}"
									name="checkBoxShopAsso_{$table}[{$shopID}]"
									id="checkedBox_{$shopID}"
									{if $checked} checked="checked"{/if} 
									{if $groupData['disable_shops']} readonly="readonly" onclick="return false"{/if}
									/>
								{$shopData['name']}
							</label>
						</td>
					</tr>
					{assign var=j value=$j+1}
				{/foreach}
			{/if}
		{/foreach}
	</table>
</div>
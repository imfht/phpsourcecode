{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file='helpers/form/form.tpl'}

{block name="input"}
	{if $input.type == 'text_customer'}
		<span class="normal-text">{$customer->lastname}{$customer->firstname}</span>
		<p style="clear: both">
			<a href="{$url_customer}">{l s='View details on customer page'}</a>
		</p>
	{elseif $input.type == 'text_order'}
		<span class="normal-text">{$text_order}</span>
		<p style="clear: both">
			<a href="{$url_order}">{l s='View details on order page'}</a>
		</p>
	{elseif $input.type == 'list_products'}
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td class="col-left">&nbsp;</td>
				<td>
					<table cellspacing="0" cellpadding="0" class="table">
					<tr>
						<th style="width: 100px;">{l s='Reference'}</th>
						<th>{l s='Product name'}</th>
						<th>{l s='Quantity'}</th>
						<th>{l s='Action'}</th>
					</tr>

					{foreach $returnedCustomizations as $returnedCustomization}
						<tr>
							<td>{$returnedCustomization['reference']}</td>
							<td class="center">{$returnedCustomization['name']}</td>
							<td class="center">{$returnedCustomization['product_quantity']|intval}</td>
							<td class="center">
								<a href="{$current}&deleteorder_return_detail&id_order_detail={$returnedCustomization['id_order_detail']}&id_order_return={$id_order_return}&id_customization={$returnedCustomization['id_customization']}&token={$token}">
									<img src="../img/admin/delete.gif">
								</a>
							</td>
						</tr>
						{foreach $customizationDatas as $type => $datas}
							<tr>
								<td colspan="4">
								{if $type == 'type_file'}
									<ul style="margin: 4px 0px 4px 0px; padding: 0px; list-style-type: none;">
									{foreach $datas a $data name='loop'}
										<li style="display: inline; margin: 2px;">
											<a href="displayImage.php?img={$data['value']}&name={$order->id|intval}-file{$loop.iteration}" target="_blank"><img src="{$picture_folder}{$data['value']}_small" alt="" /></a>
										</li>
									{/foreach}
									</ul>
								{elseif $type == 'type_textfield'}
									<ul style="margin: 0px 0px 4px 0px; padding: 0px 0px 0px 6px; list-style-type: none;">
										{foreach $datas as $data name='loop'}
											<li>{if $data['name']}$data['name']{else}{l s='Text #%d' sprintf=$loop.iteration}{/if}{l s=':'} {$data['value']}</li>
										{/foreach}
									</ul>
								{/if}
								</td>
							</tr>
						{/foreach}
					{/foreach}
			
					{* Classic products *}
					{foreach $products as $k => $product}
						{if !isset($quantityDisplayed[$product['id_order_detail']]) || $product['product_quantity']|intval > $quantityDisplayed[$product['id_order_detail']]|intval}
							<tr>
								<td>{$product['product_reference']}</td>
								<td class="center">{$product['product_name']}</td>
								<td class="center">{$product['product_quantity']}</td>
								<td class="center">
									<a href="{$current}&deleteorder_return_detail&id_order_detail={$product['id_order_detail']}&id_order_return={$id_order_return}&token={$token}">
										<img src="../img/admin/delete.gif">
									</a>
								</td>
							</tr>
						{/if}
					{/foreach}
					</table>
				</td>
			</tr>
		</table>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
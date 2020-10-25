{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{if (isset($quantity_discounts) && count($quantity_discounts) > 0)}
<!-- quantity discount -->
<ul class="idTabs clearfix">
	<li><a href="#discount" style="cursor: pointer" class="selected" data-ajax="false">{l s='Quantity discount'}</a></li>
</ul>
<div id="quantityDiscount">
	<table class="std">
		<thead>
			<tr>
				<th>{l s='product'}</th>
				<th>{l s='from (qty)'}</th>
				<th>{l s='discount'}</th>
			</tr>
		</thead>
		<tbody>
			<tr id="noQuantityDiscount">
				<td colspan='3'>{l s='There is not any quantity discount for this product.'}</td>
			</tr>
			{foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
			<tr id="quantityDiscount_{$quantity_discount.id_product_attribute}">
				<td>
					{if (isset($quantity_discount.attributes) && ($quantity_discount.attributes))}
						{$product->getProductName($quantity_discount.id_product, $quantity_discount.id_product_attribute)}
					{else}
						{$product->getProductName($quantity_discount.id_product)}
					{/if}
				</td>
				<td>{$quantity_discount.quantity|intval}</td>
				<td>
					{if $quantity_discount.price != 0 OR $quantity_discount.reduction_type == 'amount'}
						-{convertPrice price=$quantity_discount.real_value|floatval}
					{else}
						-{$quantity_discount.real_value|floatval}%
					{/if}
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>
{/if}

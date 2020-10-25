{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<div class="content_prices">
	{if $product->online_only}
	<p class="online_only">{l s='Online only'}</p>
	{/if}
	
	<div class="price">
		{if !$priceDisplay || $priceDisplay == 2}
			{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL)}
			{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
		{elseif $priceDisplay == 1}
			{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL)}
			{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
		{/if}
	
		<p class="our_price_display">
		{if $priceDisplay >= 0 && $priceDisplay <= 2}
			<span id="our_price_display">{convertPrice price=$productPrice}</span>
		{/if}
		</p><!-- .our_price_display -->
	
		{if $product->on_sale}
			<span class="on_sale">{l s='On sale!'}</span>
		{/if}
		{if $priceDisplay == 2}
			<span id="pretaxe_price"><span id="pretaxe_price_display">{convertPrice price=$product->getPrice(false, $smarty.const.NULL)}</span>&nbsp;{l s='tax excl.'}</span>
		{/if}
		

		{if $product->specificPrice AND $product->specificPrice.reduction}
			<p class="old_price">
			{if $priceDisplay >= 0 && $priceDisplay <= 2}
				{if $productPriceWithoutReduction > $productPrice}
					<span class="old_price_display">{convertPrice price=$productPriceWithoutReduction}</span>
				{/if}
			{/if}
			{if $product->specificPrice.reduction_type == 'percentage'}
				<span class="reduction_percent">-{$product->specificPrice.reduction*100}%</span>
			{elseif $product->specificPrice.reduction_type == 'amount'}
				<span class="reduction_amount_display">-{convertPrice price=$product->specificPrice.reduction|floatval}</span>
			{/if}
			
			</p><!-- .old_price -->
		{/if}
	
	{if $packItems|@count && $productPrice < $product->getNoPackPrice()}
		<p class="pack_price">{l s='instead of'} <span style="text-decoration: line-through;">{convertPrice price=$product->getNoPackPrice()}</span></p>
	{/if}
	
	{if $product->ecotax != 0}
		<p class="price-ecotax">{l s='include'} <span id="ecotax_price_display">{if $priceDisplay == 2}{$ecotax_tax_exc|convertAndFormatPrice}{else}{$ecotax_tax_inc|convertAndFormatPrice}{/if}</span> {l s='for green tax'}
			{if $product->specificPrice AND $product->specificPrice.reduction}
			<br />{l s='(not impacted by the discount)'}
			{/if}
		</p>
	{/if}
	
	{if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
		 {math equation="pprice / punit_price"  pprice=$productPrice  punit_price=$product->unit_price_ratio assign=unit_price}
		<p class="unit-price"><span id="unit_price_display">{convertPrice price=$unit_price}</span> {l s='per'} {$product->unity|escape:'htmlall':'UTF-8'}</p>
	{/if}
	</div><!-- .price -->
</div><!-- .content_prices -->
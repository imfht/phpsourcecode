{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!-- MODULE Block specials -->
<div id="special_block_right" class="block products_block exclusive blockspecials">
	<h4><a href="{$link->getPageLink('prices-drop')}" title="{l s='Specials' mod='blockspecials'}">{l s='Specials' mod='blockspecials'}</a></h4>
	<div class="block_content">

{if $special}
		<ul class="products clearfix">
			<li class="product_image">
				<a href="{$special.link}"><img src="{$link->getImageLink($special.link_rewrite, $special.id_image, 'medium_default')}" alt="{$special.legend|escape:html:'UTF-8'}" height="{$mediumSize.height}" width="{$mediumSize.width}" title="{$special.name|escape:html:'UTF-8'}" /></a>
			</li>
			<li>
				{if !$PS_CATALOG_MODE}
					{if $special.specific_prices}
						{assign var='specific_prices' value=$special.specific_prices}
						{if $specific_prices.reduction_type == 'percentage' && ($specific_prices.from == $specific_prices.to OR ($smarty.now|date_format:'%Y-%m-%d %H:%M:%S' <= $specific_prices.to && $smarty.now|date_format:'%Y-%m-%d %H:%M:%S' >= $specific_prices.from))}
							<span class="reduction"><span>-{$specific_prices.reduction*100|floatval}%</span></span>
						{/if}
					{/if}
				{/if}

					<h5><a href="{$special.link}" title="{$special.name|escape:html:'UTF-8'}">{$special.name|escape:html:'UTF-8'}</a></h5>
				{if !$PS_CATALOG_MODE}
					<span class="price-discount">{if !$priceDisplay}{displayWtPrice p=$special.price_without_reduction}{else}{displayWtPrice p=$priceWithoutReduction_tax_excl}{/if}</span>
					<span class="price">{if !$priceDisplay}{displayWtPrice p=$special.price}{else}{displayWtPrice p=$special.price_tax_exc}{/if}</span>
				{/if}
			</li>
		</ul>
		<p>
			<a href="{$link->getPageLink('prices-drop')}" title="{l s='All specials' mod='blockspecials'}">&raquo; {l s='All specials' mod='blockspecials'}</a>
		</p>
{else}
		<p>{l s='No specials at this time' mod='blockspecials'}</p>
{/if}
	</div>
</div>
<!-- /MODULE Block specials -->
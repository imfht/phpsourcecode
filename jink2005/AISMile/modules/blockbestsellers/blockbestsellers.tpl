{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!-- MODULE Block best sellers -->
<div id="best-sellers_block_right" class="block products_block">
	<h4><a href="{$link->getPageLink('best-sales')}">{l s='Top sellers' mod='blockbestsellers'}</a></h4>
	<div class="block_content">
	{if $best_sellers|@count > 0}
		<ul class="product_images">
			{foreach from=$best_sellers item=product name=myLoop}
			<li class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} clearfix">
				<a href="{$product.link}" title="{$product.legend|escape:'htmlall':'UTF-8'}" class="content_img clearfix">
					<span class="number">{$smarty.foreach.myLoop.iteration}</span>
					<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')}" height="{$smallSize.height}" width="{$smallSize.width}" alt="{$product.legend|escape:'htmlall':'UTF-8'}" />
				
				</a>
				<p><a href="{$product.link}" title="{$product.legend|escape:'htmlall':'UTF-8'}">
					{$product.name|strip_tags:'UTF-8'|escape:'htmlall':'UTF-8'}<br />
					<span class="price">{$product.price}</span>
				</a></p>
			</li>
		{/foreach}
		</ul>
		<p class="lnk"><a href="{$link->getPageLink('best-sales')}" title="{l s='All best sellers' mod='blockbestsellers'}" class="button_large">&raquo; {l s='All best sellers' mod='blockbestsellers'}</a></p>
	{else}
		<p>{l s='No best sellers at this time' mod='blockbestsellers'}</p>
	{/if}
	</div>
</div>
<!-- /MODULE Block best sellers -->

{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!-- Block Viewed products -->
<div id="viewed-products_block_left" class="block products_block">
	<h4>{l s='Viewed products' mod='blockviewed'}</h4>
	<div class="block_content">
		<ul class="products clearfix">
			{foreach from=$productsViewedObj item=viewedProduct name=myLoop}
				<li class="clearfix{if $smarty.foreach.myLoop.last} last_item{elseif $smarty.foreach.myLoop.first} first_item{else} item{/if}">
					<a href="{$viewedProduct->product_link}" title="{l s='More about' mod='blockviewed'} {$viewedProduct->name|escape:html:'UTF-8'}" class="content_img">
					<img src="{if isset($viewedProduct->id_image) && $viewedProduct->id_image}{$link->getImageLink($viewedProduct->link_rewrite, $viewedProduct->cover, 'medium_default')}{else}{$img_prod_dir}{$lang_iso}-default-medium_default.jpg{/if}" height="{$mediumSize.height}" width="{$mediumSize.width}" alt="{$viewedProduct->legend|escape:html:'UTF-8'}" />
					</a>
					<div class="text_desc">
						<h5><a href="{$viewedProduct->product_link}" title="{l s='More about' mod='blockviewed'} {$viewedProduct->name|escape:html:'UTF-8'}">{$viewedProduct->name|truncate:14:'...'|escape:html:'UTF-8'}</a></h5>
						<p><a href="{$viewedProduct->product_link}" title="{l s='More about' mod='blockviewed'} {$viewedProduct->name|escape:html:'UTF-8'}">{$viewedProduct->description_short|strip_tags:'UTF-8'|truncate:44}</a></p>
					</div>
				</li>
			{/foreach}
		</ul>
	</div>
</div>

{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<div class="view_product">
	{if isset($images) && count($images) > 0}
	<!-- thumbnails -->
	<div data-role="header" class="ui-bar-a list_view">
		{assign var=image_cover value=$product->getCover($product->id)}
		{assign var=imageIds value="`$product->id`-`$image_cover.id_image`"}
		<img id="bigpic" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'large_default')}" alt="{$product->name|escape:'htmlall':'UTF-8'}" />
		<div class="thumbs_list">
			<ul id="gallery" class="thumbs_list_frame clearfix">
			{foreach from=$images item=image name=thumbnails}
				{assign var=imageIds value="`$product->id`-`$image.id_image`"}
				<li id="thumbnail_{$image.id_image}">
					<img id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'medium_default')}" alt="{$image.legend|htmlspecialchars}" height="{$mediumSize.height}" width="{$mediumSize.width}" data-large="{$link->getImageLink($product->link_rewrite, $imageIds, 'thickbox_default')}" />
				</li>
			{/foreach}
			</ul>
		</div>
	</div>
	{/if}
</div>

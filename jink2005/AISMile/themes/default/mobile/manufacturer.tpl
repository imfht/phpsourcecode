{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{capture assign='page_title'}{$manufacturer->name|escape:'htmlall':'UTF-8'}{/capture}
{include file='./page-title.tpl'}

{include file="$tpl_dir./errors.tpl"}

{if !isset($errors) OR !sizeof($errors)}

<div data-role="content" id="content">
	<p><a data-role="button" data-icon="arrow-l" data-theme="a" data-mini="true" data-inline="true" href="{$link->getPageLink('manufacturer', true)}" data-ajax="false">{l s='Manufacturers'}</a></p>
	{if !empty($manufacturer->description) || !empty($manufacturer->short_description)}
		<div class="category_desc clearfix">
			{if !empty($manufacturer->short_description)}
				<p>{$manufacturer->short_description}</p>
				<p class="hide_desc">{$manufacturer->description}</p>
				<a href="#" data-theme="f" data-role="button" data-mini="true" data-inline="true" data-icon="arrow-d" class="lnk_more" onclick="$(this).prev().slideDown('slow'); $(this).hide(); return false;" data-ajax="false">{l s='More'}</a>
			{else}
				<p>{$manufacturer->description}</p>
			{/if}
		</div><!-- .category_desc -->
	{/if}
	
	{if $products}
			<div class="clearfix">
				{include file="./category-product-sort.tpl" container_class="container-sort"}
			</div>
			<hr width="99%" align="center" size="2"/>
			{include file="./pagination.tpl"}
			{include file="./category-product-list.tpl" products=$products}
			{include file="./pagination.tpl"}
			
	{else}
		<p class="warning">{l s='No products for this manufacturer.'}</p>
	{/if}
		{include file='./sitemap.tpl'}
</div><!-- #content -->
{/if}

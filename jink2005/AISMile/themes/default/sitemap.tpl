{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{capture name=path}{l s='Sitemap'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{l s='Sitemap'}</h1>
<div id="sitemap_content" class="clearfix">
	<div class="sitemap_block">
		<h3>{l s='Our offers'}</h3>
		<ul>
			<li><a href="{$link->getPageLink('new-products')}">{l s='New products'}</a></li>
			{if !$PS_CATALOG_MODE}
			<li><a href="{$link->getPageLink('best-sales')}">{l s='Top sellers'}</a></li>
			<li><a href="{$link->getPageLink('prices-drop')}">{l s='Price drop'}</a></li>
			{/if}
			{if $display_manufacturer_link OR $PS_DISPLAY_SUPPLIERS}<li><a href="{$link->getPageLink('manufacturer')}">{l s='Manufacturers'}</a></li>{/if}
			{if $display_supplier_link OR $PS_DISPLAY_SUPPLIERS}<li><a href="{$link->getPageLink('supplier')}">{l s='Suppliers'}</a></li>{/if}
		</ul>
	</div>
	<div class="sitemap_block">
		<h3>{l s='Your Account'}</h3>
		<ul>
			<li><a href="{$link->getPageLink('my-account', true)}">{l s='Your Account'}</a></li>
			<li><a href="{$link->getPageLink('identity', true)}">{l s='Personal information'}</a></li>
			<li><a href="{$link->getPageLink('addresses', true)}">{l s='Addresses'}</a></li>
			{if $voucherAllowed}<li><a href="{$link->getPageLink('discount', true)}">{l s='Discounts'}</a></li>{/if}
			<li><a href="{$link->getPageLink('history', true)}">{l s='Order history'}</a></li>
		</ul>
	</div>
	<br class="clear" />
</div>
<div id="listpage_content">
	<div class="categTree">
		<h3>{l s='Categories'}</h3>
		<div class="tree_top"><a href="{$base_dir_ssl}">{$categoriesTree.name|escape:'htmlall':'UTF-8'}</a></div>
		<ul class="tree">
		{if isset($categoriesTree.children)}
			{foreach $categoriesTree.children as $child}
				{if $child@last}
					{include file="$tpl_dir./category-tree-branch.tpl" node=$child last='true'}
				{else}
					{include file="$tpl_dir./category-tree-branch.tpl" node=$child}
				{/if}
			{/foreach}
		{/if}
		</ul>
	</div>
	<div class="categTree">
		<h3>{l s='Pages'}</h3>
		<div class="tree_top"><a href="{$categoriescmsTree.link}">{$categoriescmsTree.name|escape:'htmlall':'UTF-8'}</a></div>
		<ul class="tree">
			{if isset($categoriescmsTree.children)}
				{foreach $categoriescmsTree.children as $child}
					{if (isset($child.children) && $child.children|@count > 0) || $child.cms|@count > 0}
						{include file="$tpl_dir./category-cms-tree-branch.tpl" node=$child}
					{/if}
				{/foreach}
			{/if}
			{foreach from=$categoriescmsTree.cms item=cms name=cmsTree}
				<li><a href="{$cms.link|escape:'htmlall':'UTF-8'}" title="{$cms.meta_title|escape:'htmlall':'UTF-8'}">{$cms.meta_title|escape:'htmlall':'UTF-8'}</a></li>
			{/foreach}
			<li><a href="{$link->getPageLink('contact', true)}">{l s='Contact'}</a></li>
			{if $display_store}<li class="last"><a href="{$link->getPageLink('stores')}" title="{l s='鎴戜滑鐨勫晢搴�'}">{l s='Our stores'}</a></li>{/if}
		</ul>
	</div>
</div>

{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
<div id="hook_mobile_top_site_map">
{hook h="displayMobileTopSiteMap"}
</div>
<hr width="99%" align="center" size="2" class=""/>

{if isset($categoriesTree.children)}
	<h2>{l s='Our offers'}</h2>

	<ul data-role="listview" data-inset="true">
		{for $i=0 to 4}
			{if isset($categoriesTree.children.$i)}
				<li data-icon="arrow-d">
					<a href="{$categoriesTree.children.$i.link|escape:'htmlall':'UTF-8'}" title="{$categoriesTree.children.$i.desc|escape:'htmlall':'UTF-8'}">
						{$categoriesTree.children.$i.name|escape:'htmlall':'UTF-8'}
					</a>
				</li>
			{/if}
		{/for}
		<li>
			{l s='All categories'}
			<ul data-role="listview" data-inset="true">
				{foreach $categoriesTree.children as $child}
					{include file="./category-tree-branch.tpl" node=$child last='true'}
				{/foreach}
			</ul>
		</li>
	</ul>
{/if}

<hr width="99%" align="center" size="2" class=""/>
<h2>{l s='Sitemap'}</h2>
<ul data-role="listview" data-inset="true" id="category">
	{if $controller_name != 'index'}<li><a href="{$link->getPageLink('index', true)}">{l s='Home'}</a></li>{/if}
	<li>{l s='Our offers'}
		<ul data-role="listview" data-inset="true">
			<li><a href="{$link->getPageLink('new-products')}" title="{l s='New products'}">{l s='New products'}</a></li>
			{if !$PS_CATALOG_MODE}
			<li><a href="{$link->getPageLink('prices-drop')}" title="{l s='Price drop'}">{l s='Price drop'}</a></li>
			<li><a href="{$link->getPageLink('best-sales', true)}" title="{l s='Top sellers'}">{l s='Top sellers'}</a></li>
			{/if}
			{if $display_manufacturer_link OR $PS_DISPLAY_SUPPLIERS}<li><a href="{$link->getPageLink('manufacturer')}">{l s='Manufacturers'}</a></li>{/if}
			{if $display_supplier_link OR $PS_DISPLAY_SUPPLIERS}<li><a href="{$link->getPageLink('supplier')}">{l s='Suppliers'}</a></li>{/if}
		</ul>
	</li>
	<li>{l s='Your Account'}
		<ul data-role="listview" data-inset="true">
			<li><a href="{$link->getPageLink('my-account', true)}">{l s='Your Account'}</a></li>
			<li><a href="{$link->getPageLink('identity', true)}">{l s='Personal information'}</a></li>
			<li><a href="{$link->getPageLink('addresses', true)}">{l s='Addresses'}</a></li>
			{if $voucherAllowed}<li><a href="{$link->getPageLink('discount', true)}">{l s='Discounts'}</a></li>{/if}
			<li><a href="{$link->getPageLink('history', true)}">{l s='Order history'}</a></li>
		</ul>
	</li>
	<li>{l s='Pages'}
		<ul data-role="listview" data-inset="true">
			{if isset($categoriescmsTree.children)}
				{foreach $categoriescmsTree.children as $child}
					{if (isset($child.children) && $child.children|@count > 0) || $child.cms|@count > 0}
						{include file="./category-cms-tree-branch.tpl" node=$child}
					{/if}
				{/foreach}
			{/if}
			{foreach from=$categoriescmsTree.cms item=cms name=cmsTree}
				<li><a href="{$cms.link|escape:'htmlall':'UTF-8'}" title="{$cms.meta_title|escape:'htmlall':'UTF-8'}">{$cms.meta_title|escape:'htmlall':'UTF-8'}</a></li>
			{/foreach}
			<li><a href="{$link->getPageLink('contact', true)}" title="{l s='Contact'}">{l s='Contact'}</a></li>
			{if $display_store}<li><a href="{$link->getPageLink('stores')}" title="{l s='Our stores'}">{l s='Our stores'}</a></li>{/if}
		</ul>
	</li>
</ul>

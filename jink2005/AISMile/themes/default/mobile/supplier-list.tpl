{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{capture assign='page_title'}{l s='Suppliers'}{/capture}
{include file='./page-title.tpl'}

<div data-role="content" id="content">
	
{if isset($errors) AND $errors}
	{include file="$tpl_dir./errors.tpl"}
{else}
	<p class="nbrmanufacturer">{strip}
		<span class="bold">
			{if $nbSuppliers == 0}
				{l s='There are no suppliers.'}
			{else}
				{if $nbSuppliers == 1}
					{l s='There is %d supplier.' sprintf=$nbSuppliers}
				{else}
					{l s='There are %d suppliers.' sprintf=$nbSuppliers}
				{/if}
			{/if}
		</span>{/strip}
	</p>
	
{if $nbSuppliers > 0}
	<ul id="suppliers_list" data-role="listview">
	{foreach $suppliers_list as $supplier}
		<li data-corners="false" data-shadow="false" data-iconshadow="true" data-inline="false" data-wrapperels="div" data-icon="arrow-r" data-iconpos="right" data-theme="c" class="clearfix {if $supplier@first}first_item{elseif $supplier@last}last_item{else}item{/if}">
			{if $supplier.nb_products > 0}
			<a href="{$link->getsupplierLink($supplier.id_supplier, $supplier.link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$supplier.name|escape:'htmlall':'UTF-8'}" data-ajax="false">
			{/if}
			<!-- logo -->
			<img src="{$img_sup_dir}{$supplier.image|escape:'htmlall':'UTF-8'}-medium_default.jpg" alt="" width="80" />
			<!-- name -->
			<h3>{$supplier.name|truncate:60:'...'|escape:'htmlall':'UTF-8'}</h3>
			<p>
			{if $supplier.nb_products == 1}
				{l s='%d product' sprintf=$supplier.nb_products|intval}
			{else}
				{l s='%d products' sprintf=$supplier.nb_products|intval}
			{/if}
			</p>
			{if $supplier.nb_products > 0}</a>{/if}
		</li>
	{/foreach}
	</ul>
	{include file="$tpl_dir./pagination.tpl"}
{/if}
{/if}
{include file='./sitemap.tpl'}
</div><!-- #content -->

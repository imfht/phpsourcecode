{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{capture assign='page_title'}
	{l s='Search'}
	{if $nbProducts > 0}
		"{if isset($search_query) && $search_query}{$search_query|escape:'htmlall':'UTF-8'}{elseif $search_tag}{$search_tag|escape:'htmlall':'UTF-8'}{elseif $ref}{$ref|escape:'htmlall':'UTF-8'}{/if}"
	{/if}
{/capture}
{include file='./page-title.tpl'}
{include file="$tpl_dir./errors.tpl"}

{if $nbProducts}
	<div data-role="content" id="content">
		<h3 class="nbresult"><span class="big">{if $nbProducts == 1}{l s='%d result has been found.' sprintf=$nbProducts|intval}{else}{l s='%d results have been found.' sprintf=$nbProducts|intval}{/if}</h3>
		
		{if !isset($instantSearch) || (isset($instantSearch) && !$instantSearch)}
		<div class="clearfix">
			{include file="./category-product-sort.tpl" container_class="container-sort"}
		</div>
		{/if}
		
		<hr width="99%" align="center" size="2"/>
		{if !isset($instantSearch) || (isset($instantSearch) && !$instantSearch)}
			{include file="./pagination.tpl"}
		{/if}
		{include file="./category-product-list.tpl" products=$products}
		
		{if !isset($instantSearch) || (isset($instantSearch) && !$instantSearch)}
		{include file="./pagination.tpl"}
		{/if}
		
		{include file='./sitemap.tpl'}
	</div><!-- #content -->
{else}
	<p class="warning">
		{if isset($search_query) && $search_query}
			{l s='No results found for your search'}&nbsp;"{if isset($search_query)}{$search_query|escape:'htmlall':'UTF-8'}{/if}"
		{elseif isset($search_tag) && $search_tag}
			{l s='No results found for your search'}&nbsp;"{$search_tag|escape:'htmlall':'UTF-8'}"
		{else}
			{l s='Please type a search keyword'}
		{/if}
	</p>
{/if}
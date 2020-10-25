{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{capture assign='page_title'}
	{if isset($cms) && !isset($category)}
		{$cms->meta_title}
	{elseif isset($category)}
		{$category->name|escape:'htmlall':'UTF-8'}
	{/if}
{/capture}
{include file='./page-title.tpl'}
<div data-role="content" id="content">
{if isset($cms) && !isset($category)}
	<div class="rte{if $content_only} content_only{/if}">
		{$cms->content}
	</div>
{elseif isset($category)}
	<div class="block-cms">
		{if isset($sub_category) & !empty($sub_category)}	
			<h3 class="bg">{l s='List of sub categories in %s:' sprintf=$category->name}</h3>
			<ul data-role="listview" data-inset="true">
				{foreach from=$sub_category item=subcategory}
					<li>
						<a href="{$link->getCMSCategoryLink($subcategory.id_cms_category, $subcategory.link_rewrite)|escape:'htmlall':'UTF-8'}" data-ajax="false">{$subcategory.name|escape:'htmlall':'UTF-8'}</a>
					</li>
				{/foreach}
			</ul>
		{/if}
		{if isset($cms_pages) & !empty($cms_pages)}
		<h3 class="bg">{l s='List of pages in %s:' sprintf=$category->name}</h3>
			<ul data-role="listview" data-inset="true">
				{foreach from=$cms_pages item=cmspages}
					<li>
						<a href="{$link->getCMSLink($cmspages.id_cms, $cmspages.link_rewrite)|escape:'htmlall':'UTF-8'}" data-ajax="false">{$cmspages.meta_title|escape:'htmlall':'UTF-8'}</a>
					</li>
				{/foreach}
			</ul>
		{/if}
	</div>
{else}
	{l s='This page does not exist.'}
{/if}
</div><!-- #content -->

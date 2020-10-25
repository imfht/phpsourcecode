{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!-- Breadcrumb -->
{if isset($smarty.capture.path)}{assign var='path' value=$smarty.capture.path}{/if}
<div class="ur_here">
<a href="{$base_dir}" title="{l s='return to Home'}">{l s='Home'}</a>
	{if isset($path) AND $path}
	    <code class="navigation-pipe" {if isset($category) && isset($category->id_category) && $category->id_category == 1}style="display:none;"{/if}>{$navigationPipe|escape:html:'UTF-8'}</code>
		{if !$path|strpos:'span'}
			<span class="navigation_page">{$path}</span> 
		{else}
			{$path}
		{/if}
	{/if}
</div>
<div class="blank10"></div>
<!-- /Breadcrumb -->
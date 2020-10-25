{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{if isset($cms_breadcrumb)}
	<div class="cat_bar">
		<span style="color: #3C8534;">{l s='Current category'} :</span>&nbsp;&nbsp;&nbsp;{$cms_breadcrumb}
	</div>
{/if}

{$content}

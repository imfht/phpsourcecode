{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{if $toolbar_btn}
	{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
{/if}

<div class="multishop-left">
	<div class="multishop-title">{l s='Multistore tree'}</div>
	{include file="controllers/shop/tree.tpl" selected_tree_id=$selected_tree_id}
</div>
<div class="multishop-right">{$content}</div>

<script type="text/javascript">
	$().ready(function(){
		if (parseInt($('.multishop-right').css('height')) > 200)
			$('.multishop-left').css('height', $('.multishop-right').css('height'));
	})
</script>
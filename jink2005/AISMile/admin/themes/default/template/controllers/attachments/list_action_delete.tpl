{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
<script>
	function confirmProductAttached(productList)
	{
		var confirm_text = "{l s='This attachment is associated with the following products:'}";
		if (confirm('{$confirm}'))
			return confirm(confirm_text + product_list);
		return false;
	}
</script>

<a href="{$href}" onclick="{if isset($product_attachements[$id])}return confirmProductAttached('{$product_list[$id]}'){else}return confirm('{$confirm}'){/if}">
	<img src="../img/admin/delete.gif" alt="{$action}" title="{$action}" />
</a>


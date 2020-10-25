{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
<html>
<head>
{if isset($css_files)}
	{foreach from=$css_files key=css_uri item=media}
		<link href="{$css_uri}" rel="stylesheet" type="text/css" media="{$media}" />
	{/foreach}
{/if}
</head>
<body>
	<script type="text/javascript">
	{if $customization_errors}
		parent.customization_errors = true;
	{else}
		parent.customization_errors = false;
		parent.$('#products_err', window.parent.document).hide();
	{/if}
	var id_selected_product = parent.$('#id_product option:selected').val();
	if (parent.searchProducts())
	{
		parent.$('#products_err', window.parent.document).html('{$customization_errors}');
		parent.$('#id_product option[value="'+id_selected_product+'"]').attr('selected', true);
		parent.$('#id_product').change();
	}
	</script>
	</body>
</html>

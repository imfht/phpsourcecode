{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{extends file="helpers/list/list_header.tpl"}
{block name=override_header}
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	$('input.quantity_received_today').live('click', function() {
		/* checks checkbox when the input is clicked */
		$(this).parents('tr:eq(0)').find('input[type=checkbox]').attr('checked', true);
	});
});
</script>
{/block}
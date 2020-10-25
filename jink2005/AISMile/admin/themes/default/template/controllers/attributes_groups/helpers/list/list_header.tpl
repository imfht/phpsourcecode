{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/list/list_header.tpl"}
{block name=leadin}
	<script type="text/javascript">
		$(document).ready(function() {
			$(location.hash).click();
		});
	</script>
{/block}
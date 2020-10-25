{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
<a href="{$href}" class="delete" {if isset($confirm)}onclick="if (confirm('{$confirm}')){ return true; }else{ event.stopPropagation(); event.preventDefault();};"{/if} title="{$action}">
	<img src="../img/admin/delete.gif" alt="{$action}" />
</a>
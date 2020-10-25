{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{if $show_toolbar}
	{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
{/if}

<div class="leadin">{block name="leadin"}{/block}</div>

{block name="override_tpl"}{/block}

{hook h='displayAdminView'}
{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst}View{/capture}
	{hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}View{/capture}
	{hook h=$hookName}
{/if}

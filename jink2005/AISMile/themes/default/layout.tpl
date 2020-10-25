{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{if !empty($display_header)}
	{include file='./header.tpl' HOOK_HEADER=$HOOK_HEADER}
{/if}
{if !empty($template)}
	{$template}
{/if}
{if !empty($display_footer)}
	{include file='./footer.tpl'}
{/if}
{if !empty($live_edit)}
	{$live_edit}
{/if}
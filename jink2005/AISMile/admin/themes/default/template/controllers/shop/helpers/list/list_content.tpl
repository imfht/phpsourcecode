{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/list/list_content.tpl"}

{block name="td_content"}
	{if $key == 'url'}
		{if isset($tr.$key)}
			<a href="{$tr.$key}" onmouseover="$(this).css('text-decoration', 'underline')" onmouseout="$(this).css('text-decoration', 'none')" target="_blank">{$tr.$key}</a>
		{else}
			<a href="{$link->getAdminLink('AdminShopUrl')|escape:'htmlall':'UTF-8'}&id_shop={$tr.$identifier}&addshop_url" class="multishop_warning">{l s='Click here to set an URL for this shop'}</a>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
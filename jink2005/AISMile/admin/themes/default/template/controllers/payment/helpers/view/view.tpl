{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
{if !$shop_context}
	<div class="warn">{l s='You have more than one shop. You need to select one to configure payment.'}</div>
{else}
		<h2 class="space">{l s='Payment modules list'}</h2>
		{if isset($url_modules)}
			<input type="button" class="button" onclick="document.location='{$url_modules}'" value="{l s='Click to see the list of payment modules.'}" /><br>
		{/if}
	
		<br />
	
		{if $display_restrictions}
			<br /><h2 class="space">{l s='Payment module restrictions'}</h2>
			{foreach $lists as $list}
				{include file='controllers/payment/restrictions.tpl'}
				<br />
			{/foreach}
		{else}
			<br />
			<div class='warn'>{l s='No payment module installed'}</div>
		{/if}
{/if}
{/block}

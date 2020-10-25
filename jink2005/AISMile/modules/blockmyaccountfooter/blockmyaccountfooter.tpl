{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!-- Block myaccount module -->
<div class="block myaccount">
	<h4><a href="{$link->getPageLink('my-account', true)}">{l s='My account' mod='blockmyaccountfooter'}</a></h4>
	<div class="block_content">
		<ul class="bullet">
			<li><a href="{$link->getPageLink('history', true)}" title="">{l s='My orders' mod='blockmyaccountfooter'}</a></li>
			{if $returnAllowed}<li><a href="{$link->getPageLink('order-follow', true)}" title="">{l s='My merchandise returns' mod='blockmyaccountfooter'}</a></li>{/if}
			<li><a href="{$link->getPageLink('order-slip', true)}" title="">{l s='My credit slips' mod='blockmyaccountfooter'}</a></li>
			<li><a href="{$link->getPageLink('addresses', true)}" title="">{l s='My addresses' mod='blockmyaccountfooter'}</a></li>
			<li><a href="{$link->getPageLink('identity', true)}" title="">{l s='My personal info' mod='blockmyaccountfooter'}</a></li>
			{if $voucherAllowed}<li><a href="{$link->getPageLink('discount', true)}" title="">{l s='My vouchers' mod='blockmyaccountfooter'}</a></li>{/if}
			{$HOOK_BLOCK_MY_ACCOUNT}
		</ul>
		<p class="logout"><a href="{$link->getPageLink('index')}?mylogout" title="{l s='Sign out' mod='blockmyaccountfooter'}">{l s='Sign out' mod='blockmyaccount'}</a></p>
	</div>
</div>
<!-- /Block myaccount module -->

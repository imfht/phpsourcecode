{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!-- MODULE Loyalty -->
<p id="loyalty">
	<img src="{$module_template_dir}images/loyalty.gif" alt="{l s='loyalty' mod='loyalty'}" class="icon" />
	{if $points > 0}
		{l s='By checking out this shopping cart you can collect up to' mod='loyalty'} <b>
		{if $points > 1}{l s='%d loyalty points' sprintf=$points mod='loyalty'}{else}{l s='%d loyalty point' sprintf=$points mod='loyalty'}{/if}</b>
		{l s='that can be converted into a voucher of' mod='loyalty'} {convertPrice price=$voucher}{if isset($guest_checkout) && $guest_checkout}<sup>*</sup>{/if}.<br />
		{if isset($guest_checkout) && $guest_checkout}<sup>*</sup> {l s='Not available for Instant checkout order' mod='loyalty'}{/if}
	{else}
		{l s='Add some products to your shopping cart to collect some loyalty points.' mod='loyalty'}
	{/if}
</p>
<!-- END : MODULE Loyalty -->
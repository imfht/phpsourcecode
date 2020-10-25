{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<p>{l s='Your order on' mod='cashondelivery'} <span class="bold">{$shop_name}</span> {l s='is complete.' mod='cashondelivery'}
	<br /><br />
	{l s='You have chosen the cash on delivery method.' mod='cashondelivery'}
	<br /><br /><span class="bold">{l s='Your order will be sent very soon.' mod='cashondelivery'}</span>
	<br /><br />{l s='For any questions or for further information, please contact our' mod='cashondelivery'} <a href="{$link->getPageLink('contact-form', true)}">{l s='customer support' mod='cashondelivery'}</a>.
</p>

{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{if $status == 'ok'}
<p>{l s='Your order on %s is complete.' sprintf=$shop_name mod='bankwire'}
		<br /><br />
		{l s='Please send us a bank wire with:' mod='bankwire'}
		<br /><br />- {l s='an amount of' mod='bankwire'} <span class="price"> <strong>{$total_to_pay}</strong></span>
		<br /><br />- {l s='to the account owner of' mod='bankwire'}  <strong>{if $bankwireOwner}{$bankwireOwner}{else}___________{/if}</strong>
		<br /><br />- {l s='with these details' mod='bankwire'}  <strong>{if $bankwireDetails}{$bankwireDetails}{else}___________{/if}</strong>
		<br /><br />- {l s='to this bank' mod='bankwire'}  <strong>{if $bankwireAddress}{$bankwireAddress}{else}___________{/if}</strong>
		{if !isset($reference)}
			<br /><br />- {l s='Do not forget to insert your order number #%d in the subject of your bank wire' sprintf=$id_order mod='bankwire'}
		{else}
			<br /><br />- {l s='Do not forget to insert your order reference %s in the subject of your bank wire.' sprintf=$reference mod='bankwire'}
		{/if}		<br /><br />{l s='An e-mail has been sent to you with this information.' mod='bankwire'}
		<br /><br /> <strong>{l s='Your order will be sent as soon as we receive your settlement.' mod='bankwire'}</strong>
		<br /><br />{l s='For any questions or for further information, please contact our' mod='bankwire'} <a href="{$link->getPageLink('contact', true)}">{l s='customer support' mod='bankwire'}</a>.
	</p>
{else}
	<p class="warning">
		{l s='We noticed a problem with your order. If you think this is an error, you can contact our' mod='bankwire'} 
		<a href="{$link->getPageLink('contact', true)}">{l s='customer support' mod='bankwire'}</a>.
	</p>
{/if}

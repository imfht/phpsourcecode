{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<div id="contact_block" class="block">
	<h4>{l s='Contact us' mod='blockcontact'}</h4>
	<div class="block_content clearfix">
			<p>{l s='Our hotline is available 24/7' mod='blockcontact'}</p>
			{if $telnumber != ''}<p class="tel"><span class="label">{l s='Phone:' mod='blockcontact'}</span>{$telnumber|escape:'htmlall':'UTF-8'}</p>{/if}
			{if $email != ''}<a href="mailto:{$email|escape:'htmlall':'UTF-8'}">{l s='Contact our hotline' mod='blockcontact'}</a>{/if}
	</div>
</div>

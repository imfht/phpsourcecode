{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!-- MODULE Block contact infos -->
<div id="block_contact_infos">
	<h4>{l s='Contact us' mod='blockcontactinfos'}</h4>
	<ul>
		{if $blockcontactinfos_company != ''}<li><strong>{$blockcontactinfos_company|escape:'htmlall':'UTF-8'}</strong></li>{/if}
		{if $blockcontactinfos_address != ''}<li><pre>{$blockcontactinfos_address|escape:'htmlall':'UTF-8'}</pre></li>{/if}
		{if $blockcontactinfos_phone != ''}<li>{l s='Tel:' mod='blockcontactinfos'} {$blockcontactinfos_phone|escape:'htmlall':'UTF-8'}</li>{/if}
		{if $blockcontactinfos_email != ''}<li>{l s='Email:' mod='blockcontactinfos'} {mailto address=$blockcontactinfos_email|escape:'htmlall':'UTF-8' encode="hex"}</li>{/if}
	</ul>
</div>
<!-- /MODULE Block contact infos -->

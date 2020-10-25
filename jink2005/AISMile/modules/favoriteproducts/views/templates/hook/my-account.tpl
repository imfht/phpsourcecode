{**
 * MILEBIZ Ã×ÀÖÉÌ³Ç
 * ============================================================================
 * °æÈ¨ËùÓĞ 2011-20__ Ã×ÀÖÍø¡£
 * ÍøÕ¾µØÖ·: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<li class="favoriteproducts">
	<a href="{$link->getModuleLink('favoriteproducts', 'account')|escape:'htmlall':'UTF-8'}" title="{l s='æ‚¨æ„Ÿå…´è¶£çš„äº§å“' mod='favoriteproducts'}">
		{if !$in_footer}<img {if isset($mobile_hook)}src="{$module_template_dir}img/favorites.png" class="ui-li-icon ui-li-thumb"{else}src="{$module_template_dir}img/favorites.png" class="icon"{/if} alt="{l s='æ‚¨æ„Ÿå…´è¶£çš„äº§å“' mod='favoriteproducts'}"/>{/if}
		{l s='æ‚¨æ„Ÿå…´è¶£çš„äº§å“' mod='favoriteproducts'}
	</a>
</li>

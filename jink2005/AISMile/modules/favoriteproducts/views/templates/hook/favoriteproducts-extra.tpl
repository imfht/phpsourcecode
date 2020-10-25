{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{if !$isCustomerFavoriteProduct AND $isLogged}
<li id="favoriteproducts_block_extra_add" class="add">
	{l s='Add this product to my favorites' mod='favoriteproducts'}
</li>
{/if}
{if $isCustomerFavoriteProduct AND $isLogged}
<li id="favoriteproducts_block_extra_remove">
	{l s='Remove this product from my favorites' mod='favoriteproducts'}
</li>
{/if}

<li id="favoriteproducts_block_extra_added">
	{l s='Remove this product from my favorites' mod='favoriteproducts'}
</li>
<li id="favoriteproducts_block_extra_removed">
	{l s='Add this product to my favorites' mod='favoriteproducts'}
</li>
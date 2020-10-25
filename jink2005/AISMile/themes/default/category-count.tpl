{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{if $category->id == 1 OR $nb_products == 0}
	{l s='There are no products.'}
{else}
	{if $nb_products == 1}
		{l s='There is %d product.' sprintf=$nb_products}
	{else}
		{l s='There are %d products.' sprintf=$nb_products}
	{/if}
{/if}
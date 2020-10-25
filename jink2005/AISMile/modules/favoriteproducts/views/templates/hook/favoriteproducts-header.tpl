{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
<script type="text/javascript">
	var favorite_products_url_add = '{$link->getModuleLink('favoriteproducts', 'actions', ['process' => 'add'], true)}';
	var favorite_products_url_remove = '{$link->getModuleLink('favoriteproducts', 'actions', ['process' => 'remove'], true)}';
{if isset($smarty.get.id_product)}
	var favorite_products_id_product = '{$smarty.get.id_product|intval}';
{/if} 
</script>

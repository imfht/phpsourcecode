{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<script type="text/javascript">
$(document).ready(function() {ldelim}
	{if isset($nb_people)}$.jGrowl('{if $nb_people == 1}{l s='%d person is currently watching this product' sprintf=$nb_people mod='producttooltip'}{else}{l s='%d people are currently watching this product' sprintf=$nb_people mod='producttooltip'}{/if}', {literal}{ life: 3500 }{/literal});{/if}
	{if isset($date_last_order)}$.jGrowl('{l s='This product was bought last' mod='producttooltip'} {dateFormat date=$date_last_order full=1}', {literal}{ life: 3500 }{/literal});{/if}
	{if isset($date_last_cart)}$.jGrowl('{l s='This product was added to cart last' mod='producttooltip'} {dateFormat date=$date_last_cart full=1}', {literal}{ life: 3500 }{/literal});{/if}
{rdelim});
</script>

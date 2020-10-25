{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/form/form.tpl"}

{block name="input"}
	{if ($input.type == "description")}
		<p>{$input.text}</p>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="after"}
	<script type="text/javascript">
		startingData = new Array();
		{foreach from=$products item=product key=key}
			startingData[{$key}] = new Array(
				'{$product.details->name}', 
				'{$product.id_product}', 
				{$product.x_axis},
				{$product.y_axis},
				{$product.zone_width},
				{$product.zone_height});
		{/foreach}
	</script>
{/block}

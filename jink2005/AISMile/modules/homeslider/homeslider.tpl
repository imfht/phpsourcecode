{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!-- Module HomeSlider -->
{if isset($homeslider)}
<script type="text/javascript">
{if isset($homeslider_slides) && $homeslider_slides|@count > 1}
	{if $homeslider.loop == 1}
		var homeslider_loop = true;
	{else}
		var homeslider_loop = false;
	{/if}
{else}
	var homeslider_loop = false;
{/if}
var homeslider_speed = {$homeslider.speed};
var homeslider_pause = {$homeslider.pause};
</script>
{/if}
{if isset($homeslider_slides)}
<ul id="homeslider">
{foreach from=$homeslider_slides item=slide}
	{if $slide.active}
		<li><a href="{$slide.url}" title="{$slide.description}"><img src="{$smarty.const._MODULE_DIR_}/homeslider/images/{$slide.image}" alt="{$slide.legend}" title="{$slide.description}" height="{$homeslider.height}" width="{$homeslider.width}" /></a></li>
	{/if}
{/foreach}
</ul>
{/if}
<!-- /Module HomeSlider -->

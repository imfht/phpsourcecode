{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!-- Module Editorial -->
<div id="editorial_block_center" class="editorial_block">
	{if $editorial->body_home_logo_link}<a href="{$editorial->body_home_logo_link|escape:'htmlall':'UTF-8'}" title="{$editorial->body_title|escape:'htmlall':'UTF-8'|stripslashes}">{/if}
	{if $homepage_logo}<img src="{$link->getMediaLink($image_path)}" alt="{$editorial->body_title|escape:'htmlall':'UTF-8'|stripslashes}" {if $image_width}width="{$image_width}"{/if} {if $image_height}height="{$image_height}" {/if}/>{/if}
	{if $editorial->body_home_logo_link}</a>{/if}
	{if $editorial->body_logo_subheading}<p id="editorial_image_legend">{$editorial->body_logo_subheading|stripslashes}</p>{/if}
	{if $editorial->body_title}<h1>{$editorial->body_title|stripslashes}</h1>{/if}
	{if $editorial->body_subheading}<h2>{$editorial->body_subheading|stripslashes}</h2>{/if}
	{if $editorial->body_paragraph}<div class="rte">{$editorial->body_paragraph|stripslashes}</div>{/if}
</div>
<!-- /Module Editorial -->

{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<div class="translatable">
{foreach from=$languages item=language}
<div class="lang_{$language.id_lang}" style="{if !$language.is_default}display:none;{/if}float: left;">
	<textarea cols="100" rows="10" type="text" id="{$input_name}_{$language.id_lang}" 
		name="{$input_name}_{$language.id_lang}" 
		class="autoload_rte" >{if isset($input_value[$language.id_lang])}{$input_value[$language.id_lang]|htmlentitiesUTF8}{/if}</textarea>
	<span class="counter" max="{if isset($max)}{$max}{else}none{/if}"></span>
	<span class="hint" name="help_box">{$hint|default:''}<span class="hint-pointer">&nbsp;</span></span>
</div>
{/foreach}
</div>
<script type="text/javascript">
	var iso = '{$iso_tiny_mce}';
	var pathCSS = '{$smarty.const._THEME_CSS_DIR_}';
	var ad = '{$ad}';
</script>

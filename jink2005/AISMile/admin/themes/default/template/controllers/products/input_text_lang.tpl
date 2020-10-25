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
	<input size="30" type="text" id="{$input_name}_{$language.id_lang}" 
	name="{$input_name}_{$language.id_lang}"
		value="{$input_value[$language.id_lang]|htmlentitiesUTF8|default:''}"
		onchange="updateFriendlyURL();"
		onkeyup="if (isArrowKey(event)) return ;updateFriendlyURL();"/>
</div>
{/foreach}
</div>

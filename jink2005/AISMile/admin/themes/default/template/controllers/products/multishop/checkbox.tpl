{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{if isset($display_multishop_checkboxes) && $display_multishop_checkboxes}
	{if isset($multilang) && $multilang}
		{if isset($only_checkbox)}
			{foreach from=$languages item=language}
				<input type="checkbox" name="multishop_check[{$field}][{$language.id_lang}]" value="1" onclick="ProductMultishop.checkField(this.checked, '{$field}_{$language.id_lang}', '{$type}')" {if !empty($multishop_check[$field][$language.id_lang])}checked="checked"{/if} />
			{/foreach}
		{else}
			<div class="multishop_product_checkbox">
				{foreach from=$languages item=language}
					<div class="multishop_lang_{$language.id_lang}" style="{if !$language.is_default}display: none;{/if}">
						<input type="checkbox" name="multishop_check[{$field}][{$language.id_lang}]" value="1" onclick="ProductMultishop.checkField(this.checked, '{$field}_{$language.id_lang}', '{$type}')" {if !empty($multishop_check[$field][$language.id_lang])}checked="checked"{/if} />
					</div>
				{/foreach}
			</div>
		{/if}
	{else}
		{if isset($only_checkbox)}
			<input type="checkbox" name="multishop_check[{$field}]" value="1" onclick="ProductMultishop.checkField(this.checked, '{$field}', '{$type}')" {if !empty($multishop_check[$field])}checked="checked"{/if} />
		{else}
			<div class="multishop_product_checkbox">
				<input type="checkbox" name="multishop_check[{$field}]" value="1" onclick="ProductMultishop.checkField(this.checked, '{$field}', '{$type}')" {if !empty($multishop_check[$field])}checked="checked"{/if} />
			</div>
		{/if}
	{/if}
{/if}
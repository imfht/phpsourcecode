{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{extends file="helpers/form/form.tpl"}

{block name="label"}
	{if $input.type == "select_template"}
		<div id="tpl" style="display:{if isset($fields_value.send_email) && $fields_value.send_email}block{else}none{/if}">
	{/if}
	{$smarty.block.parent}
{/block}

{block name="input"}
	{if $input.type == "select_template"}
		<div class="translatable">
			{foreach $languages as $language}
				<div class="lang_{$language.id_lang}" id="{$input.name}_{$language.id_lang}" style="display:{if $language.id_lang == $defaultFormLanguage}block{else}none{/if}; float: left;">
					<select name="{$input.name}_{$language.id_lang}"
							id="{$input.name}_select_{$language.id_lang}"
							{if isset($input.multiple)}multiple="multiple" {/if}
							{if isset($input.size)}size="{$input.size}"{/if}
							{if isset($input.onchange)}onchange="{$input.onchange}"{/if}>
						{foreach $input.options.query AS $option}
							<option value="{$option[$input.options.id]}"
								{if isset($input.multiple)}
									{foreach $fields_value[$input.name] as $field_value}
										{if $field_value == $option[$input.options.id]}selected="selected"{/if}
									{/foreach}
								{else}
									{if isset($fields_value[$input.name][$language.id_lang]) && ($fields_value[$input.name][$language.id_lang] == $option[$input.options.id])}selected="selected"{/if}
								{/if}
							>{$option[$input.options.name]|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
					{if isset($input.hint)}<span class="hint" name="help_box">{$input.hint}<span class="hint-pointer">&nbsp;</span></span>{/if}
					<img onclick="viewTemplates('#template_select_{$language.id_lang}', '../mails/{$language.iso_code}/', '.html');"
						src="../img/t/AdminFeatures.gif" class="pointer" alt="{l s='Preview'}" title="{l s='Preview'}" />
				</div>
			{/foreach}
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="field"}
	{$smarty.block.parent}
	{if $input.type == "select_template"}
		</div>
	{/if}
{/block}

{block name="script"}
	$(document).ready(function() {
		$('#send_email_on').click(function() {
			$('#tpl').slideToggle();
		});
	});
{/block}
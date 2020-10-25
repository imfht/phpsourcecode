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
	{if $input.type == 'special'}
		<div id="#resultCheckLangPack">
			<p id="lang_pack_loading" style="display:none"><img src="../img/admin/{$input.img}" alt="" /> {$input.text}</p>
			<p id="lang_pack_msg" style="display:none"></p>
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name=script}
		var langPackOk = "<img src=\"{$smarty.const._PS_IMG_}admin/information.png\" alt=\"\" /> {l s='A language pack is available for this ISO (name is'}";
		var langPackVersion = "{l s='The compatible MileBiz version for this language and your system is:'}";
		var langPackInfo = "{l s='After creating the language, you can import the content of the language pack, which you can download above under "Localization - Translations"'}";
		var noLangPack = "<img src=\"{$smarty.const._PS_IMG_}admin/information.png\" alt=\"\" /> {l s='No language pack available on milebiz.com for this ISO code'}";
		var download = "{l s='Download'}";

	$(document).ready(function() {
		$('#iso_code').keyup(function(e) {
			e.preventDefault();
			checkLangPack("{$token}");
		});
	});

{/block}

{block name="other_fieldsets"}

	{if isset($fields['new'])}
		<br /><br />
		<fieldset style="width:572px;">
			{foreach $fields['new'] as $key => $field}
				{if $key == 'legend'}
					<legend>
						{if isset($field.image)}<img src="{$field.image}" alt="{$field.title}" />{/if}
						{$field.title}
					</legend>
					<p>{l s='This language is NOT complete and cannot be used in the Front or Back Office because some files are missing.'}</p>
					<br />
				{elseif $key == 'list_files'}
					{foreach $field as $list}
						<label>{$list.label}</label>
						<div class="margin-form" style="margin-top:4px;">
							{foreach $list.files as $key => $file}
								{if !file_exists($key)}
									<font color="red">
								{/if}
								{$key}
								{if !file_exists($key)}
									</font>
								{/if}
								<br />
							{/foreach}
						</div>
						<br style="clear:both;" />
					{/foreach}
				{/if}
			{/foreach}
			<br />
			<div class="small">{l s='Missing files are marked in red'}</div>
		</fieldset>
	{/if}

{/block}

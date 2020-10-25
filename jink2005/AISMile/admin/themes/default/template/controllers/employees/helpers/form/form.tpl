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
	{if $input.type == 'select_theme'}
		<select name="{$input.name}" id="{$input.name}" {if isset($input.multiple)}multiple="multiple" {/if}{if isset($input.onchange)}onchange="{$input.onchange}"{/if}>
			{foreach $input.options.query AS $option}
				<option value="{$option}"
					{if isset($input.multiple)}
						{foreach $fields_value[$input.name] as $field_value}
							{$field_value}
							{if $field_value == $option}selected="selected"{/if}
						{/foreach}
					{else}
						{if $fields_value[$input.name] == $option}selected="selected"{/if}
					{/if}
				>{$option|escape:'htmlall':'UTF-8'}</option>
			{/foreach}
		</select>
	{elseif $input.type == 'default_tab'}
	<select name="{$input.name}" id="{$input.name}">
		{foreach $input.options AS $option}
			<optgroup label="{$option.name|escape:'htmlall':'UTF-8'}"></optgroup>
			{foreach $option.children AS $children}
				<option value="{$children.id_tab}" {if $fields_value[$input.name] == $children.id_tab}selected="selected"{/if}>&nbsp;&nbsp;{$children.name|escape:'htmlall':'UTF-8'}</option>
			{/foreach}
		{/foreach}
	</select>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name=script}
	$(document).ready(function(){
		$('select[name=id_profile]').change(function(){
			ifSuperAdmin($(this));

			$.ajax({
				url: "{$link->getAdminLink('AdminEmployees')}",
				cache: false,
				data : {
					ajax : '1',
					action : 'getTabByIdProfile',
					id_profile : $(this).val()
				},
				dataType : 'json',
				success : function(resp,textStatus,jqXHR)
				{
					if (resp != false)
					{
						$('select[name=default_tab]').html('');
						$.each(resp, function(key, r){
							if (r.id_parent == 0)
							{
								$('select[name=default_tab]').append('<optgroup label="'+r.name+'"></optgroup>');
								$.each(r.children, function(k, value){
									$('select[name=default_tab]').append('<option value="'+r.id_tab+'">'+value.name+'</option>')
								});
							}
						});
					}
				}
			});
		});
		ifSuperAdmin($('select[name=id_profile]'));
	});

	function ifSuperAdmin(el)
	{
		var val = $(el).val();

		if (!val || val == {$smarty.const._PS_ADMIN_PROFILE_})
		{
			$('.assoShop input[type=checkbox]').attr('disabled', true);
			$('.assoShop input[type=checkbox]').attr('checked', true);
		}
		else
			$('.assoShop input[type=checkbox]').attr('disabled', false);
	}
{/block}

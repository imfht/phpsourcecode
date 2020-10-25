{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{include file="toolbar.tpl" toolbar_btn=$toolbar_btn toolbar_scroll=$toolbar_scroll title=$title}
<div class="leadin">{block name="leadin"}{/block}</div>

<form action="{$url_submit}" id={$table}_form method="post">
	{if $display_key}
		<input type="hidden" name="show_modules" value="{$display_key}" />
	{/if}
	<fieldset>
		<legend><img src="../img/t/AdminModulesPositions.gif" />{l s='Transplant a module'}</legend>
		<label>{l s='Module'} :</label>
		<div class="margin-form">
			<select name="id_module" {if $edit_graft} disabled="disabled"{/if}>
				{foreach $modules as $module}
					<option value="{$module->id}" {if $id_module == $module->id || (!$id_module && $show_modules == $module->id)}selected="selected"{/if}>{$module->displayName|stripslashes}</option>
				{/foreach}
			</select><sup> *</sup>
		</div>
		<label>{l s='Hook into'} :</label>
		<div class="margin-form">
			<select name="id_hook" {if $edit_graft} disabled="disabled"{/if}>
				{foreach $hooks as $hook}
					<option value="{$hook['id_hook']}" {if $id_hook == $hook['id_hook']} selected="selected"{/if}>"{$hook['title']}" {l s='known as'} "{$hook['name']}"</option>
				{/foreach}
			</select><sup> *</sup>
		</div>
	
		<script type="text/javascript">
			//<![CDATA
			function position_exception_add(shopID)
			{
				var listValue = $('#em_list_'+shopID).val();
				var inputValue = $('#em_text_'+shopID).val();
				var r = new RegExp('(^|,) *'+listValue+' *(,|$)');
				if (!r.test(inputValue))
					$('#em_text_'+shopID).val(inputValue + ((inputValue.trim()) ? ', ' : '') + listValue);
			}
		
			function position_exception_remove(shopID)
			{
				var listValue = $('#em_list_'+shopID).val();
				var inputValue = $('#em_text_'+shopID).val();
				var r = new RegExp('(^|,) *'+listValue+' *(,|$)');
				if (r.test(inputValue))
				{
					var rep = '';
					if (new RegExp(', *'+listValue+' *,').test(inputValue))
						$('#em_text_'+shopID).val(inputValue.replace(r, ','));
					else if (new RegExp(listValue+' *,').test(inputValue))
						$('#em_text_'+shopID).val(inputValue.replace(r, ''));
					else
						$('#em_text_'+shopID).val(inputValue.replace(r, ''));
				}
			}
			//]]>
		</script>
	
		<label>{l s='Exceptions'} :</label>
		<div class="margin-form">
			{if !$except_diff}
				{$exception_list}
			{else}
				{foreach $exception_list_diff as $value}
					{$value}
				{/foreach}
			{/if}
			{l s='Please specify the files for which you do not want the module to be displayed'}.<br />
			{l s='Please type each filename separated by a comma'}.
			<br /><br />
		</div>
	
		<div class="margin-form">
			{if $edit_graft}
				<input type="hidden" name="id_module" value="{$id_module}" />
				<input type="hidden" name="id_hook" value="{$id_hook}" />
			{/if}
			<input type="submit" value="{l s='Save'}" name="{if $edit_graft}submitEditGraft{else}submitAddToHook{/if}" id="{$table}_form_submit_btn" class="button" />
		</div>
		<div class="small"><sup>*</sup> {l s='Required field'}</div>
	</fieldset>
</form>
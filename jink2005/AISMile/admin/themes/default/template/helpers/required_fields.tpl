{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<br />
<p>
	<a class="button" href="#" onclick="if ($('.requiredFieldsParameters:visible').length == 0) $('.requiredFieldsParameters').slideDown('slow'); else $('.requiredFieldsParameters').slideUp('slow'); return false;"><img src="../img/admin/duplicate.gif" alt="" /> {l s='Set required fields for this section'}</a>
</p>
<fieldset style="display:none" class="width1 requiredFieldsParameters">
	<legend>{l s='Required Fields'}</legend>
	<form name="updateFields" action="{$current}&submitFields=1&token={$token}" method="post">
		<p>
			<b>{l s='Select the fields you would like to be required for this section.'}</b><br />
			<table cellspacing="0" cellpadding="0" class="table width1 clear">
				<thead>
					<tr>
						<th><input type="checkbox" onclick="checkDelBoxes(this.form, 'fieldsBox[]', this.checked)" class="noborder" name="checkme"></th>
						<th>{l s='Field Name'}</th>
					</tr>
				</thead>
				<tbody>
				{foreach $table_fields as $field}
					{if !in_array($field, $required_class_fields)}
						<tr class="{if $irow++ % 2}alt_row{/if}">
							<td class="noborder"><input type="checkbox" name="fieldsBox[]" value="{$field}" {if in_array($field, $required_fields)} checked="checked"{/if} /></td>
							<td>{$field}</td>
						</tr>
					{/if}
				{/foreach}
				</tbody>
			</table><br />
			<center>
				<input style="margin-left:15px;" class="button" type="submit" value="{l s='Save'}" name="submitFields" />
			</center>
		</p>
	</form>
</fieldset>

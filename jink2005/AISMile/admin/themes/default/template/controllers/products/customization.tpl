{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{if isset($obj->id)}
	<input type="hidden" name="submitted_tabs[]" value="Customization" />
	<h4>{l s='Add or modify customizable properties'}</h4>
	
	{include file="controllers/products/multishop/check_fields.tpl" product_tab="Customization"}
	<div class="separation"></div><br />
	<table cellpadding="5" style="width:100%">
		<tr>
			<td style="width:150px;text-align:right;padding-right:10px;font-weight:bold;vertical-align:top;" valign="top">
				{include file="controllers/products/multishop/checkbox.tpl" field="uploadable_files" type="default"}
				<label>{l s='File fields:'}</label>
			</td>
			<td style="padding-bottom:5px;">
				<input type="text" name="uploadable_files" id="uploadable_files" size="4" value="{$uploadable_files|htmlentities}" />
				<p class="preference_description">{l s='Number of upload file fields displayed'}</p>
			</td>
		</tr>
		<tr>
			<td style="width:150px;text-align:right;padding-right:10px;font-weight:bold;vertical-align:top;" valign="top">
			{include file="controllers/products/multishop/checkbox.tpl" field="text_fields" type="default"}
			<label>{l s='Text fields:'}</label>
			</td>
			<td style="padding-bottom:5px;">
				<input type="text" name="text_fields" id="text_fields" size="4" value="{$text_fields|htmlentities}" />
				<p class="preference_description">{l s='Number of text fields displayed'}</p>
			</td>
		</tr>
		<tr>
			<td><div class="clear">&nbsp;</div></td>
		</tr>

		{if $has_file_labels}
			<tr>
				<td colspan="2"><div class="separation"></div></td>
			</tr>
			<tr>
				<td style="width:200px" valign="top">{l s='Define the label of the file fields:'}</td>
				<td>
					{$display_file_labels}
				</td>
			</tr>
		{/if}
		{if $has_text_labels}
			<tr>
				<td colspan="2"><div class="separation"></div></td>
			</tr>
			<tr>
				<td style="width:200px" valign="top">{l s='Define the label of the text fields:'}</td>
				<td>
					{$display_text_labels}
				</td>
			</tr>
		{/if}
	</table>
{/if}
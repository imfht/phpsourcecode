{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/options/options.tpl"}
{block name="field"}
	{if $field['type'] == 'checkbox_table'}
		<div class="margin-form" style="float: left; padding-left: 0; width: 317px; margin-top: 6px; height: 300px; overflow-y: auto;">
			<table class="table" cellspacing="0">
				<thead>
					<tr>
						<th><input type="checkbox" name="checkAll" onclick="checkDelBoxes(this.form, 'countries[]', this.checked)" /></th>
						<th>{l s='Name'}</th>
					<tr>
				</thead>
				<tbody>
					{foreach $field['list'] as $country}
						<tr>
							<td><input type="checkbox" name="countries[]" value="{$country[$field['identifier']]}" {if in_array(strtoupper($country['iso_code']), $allowed_countries)}checked="checked"{/if} /></td>
							<td>{$country['name']|escape:'htmlall':'UTF-8'}</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
		<div class="clear"></div>
		<br />
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="input"}
	{if $field['type'] == 'textarea_newlines'}
		<textarea name={$key} cols="{$field['cols']}" rows="{$field['rows']}">{$field['value']|replace:';':"\n"|escape:'htmlall':'UTF-8'}</textarea>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

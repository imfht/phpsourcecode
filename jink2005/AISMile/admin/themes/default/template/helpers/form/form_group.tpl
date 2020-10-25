{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{if count($groups) && isset($groups)}
<table cellspacing="0" cellpadding="0" class="table" style="width:28em;">
	<tr>
		<th>
			<input type="checkbox" name="checkme" id="checkme" class="noborder" onclick="checkDelBoxes(this.form, 'groupBox[]', this.checked)" />
		</th>
		<th>{l s='ID'}</th>
		<th>{l s='Group name'}</th>
	</tr>
	{foreach $groups as $key => $group}
		<tr {if $key %2}class="alt_row"{/if}>
			<td>
				{assign var=id_checkbox value=groupBox|cat:'_'|cat:$group['id_group']}
				<input type="checkbox" name="groupBox[]" class="groupBox" id="{$id_checkbox}" value="{$group['id_group']}" {if $fields_value[$id_checkbox]}checked="checked"{/if} />
			</td>
			<td>{$group['id_group']}</td>
			<td><label for="{$id_checkbox}" class="t">{$group['name']}</label></td>
		</tr>
	{/foreach}
</table>
{else}
<p>{l s='No group created'}</p>
{/if}
{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{if isset($groups)}
<hr width="99%" align="center" size="2" class="margin_less"/>

<div id="attributes">
{foreach from=$groups key=id_attribute_group item=group}
	{if $group.attributes|@count}
	<div class="attributes_group">
		{capture assign='groupName'}group_{$id_attribute_group|intval}{/capture}
		<label class="attribute_label" for="{$groupName}">{$group.name|escape:'htmlall':'UTF-8'} :</label>
		{if ($group.group_type == 'select' || $group.group_type == 'color')}
			<select name="{$groupName}" id="{$groupName}" class="attribute_select{if ($group.group_type == 'color')} select_color{/if}">
				{foreach from=$group.attributes key=id_attribute item=group_attribute}
					<option value="{$id_attribute|intval}" title="{$group_attribute|escape:'htmlall':'UTF-8'}">{$group_attribute|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
			</select>
		{elseif ($group.group_type == 'radio')}
			<fieldset data-role="controlgroup">
			{foreach from=$group.attributes key=id_attribute item=group_attribute}
				<input type="radio" class="attribute_radio" name="{$groupName}" id="{$groupName}_{$id_attribute}" value="{$id_attribute}">
				<label for="{$groupName}_{$id_attribute}">{$group_attribute|escape:'htmlall':'UTF-8'}</label>
			{/foreach}
			</fieldset>
		{/if}
	</div>
	{/if}
{/foreach}
</div><!-- #attributes -->
{/if}

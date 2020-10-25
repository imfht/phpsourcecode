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
	{if $input.type == 'color'}
		<div id="colorAttributeProperties" style="display:{if $colorAttributeProperties}block{else}none{/if}";>
	{/if}
	{$smarty.block.parent}
{/block}

{block name="field"}
	{if $input.name == 'current_texture'}
		<div class="margin-form">
			{if isset($imageTextureExists) && $imageTextureExists}
				<img src="{$imageTexture}" alt="{l s='Texture'}" />
			{else}
				{l s='None'}
			{/if}
		</div>
	{else}
		{$smarty.block.parent}
	{/if}
	{if $input.name == 'name'}
		{hook h="displayAttributeForm" id_attribute=$form_id}
	{/if}
{/block}

{block name="script"}
	var attributesGroups = {ldelim}{$strAttributesGroups}{rdelim};

	var displayColorFieldsOption = function() {
		var val = $('#id_attribute_group').val();
		if (attributesGroups[val])
			$('#colorAttributeProperties').show();
		else
			$('#colorAttributeProperties').hide();
	};
	
	displayColorFieldsOption();
	
	$('#id_attribute_group').change(displayColorFieldsOption);

	var shop_associations = {$fields[0]['form']['shop_associations']};
	var changeAssociationGroup = function()
	{
		var id_attribute_group = $('#id_attribute_group').val();
		$('.input_shop').each(function(k, item)
		{
			var id_shop = $(item).attr('shop_id');
			if (typeof shop_associations[id_attribute_group] != 'undefined' && $.inArray(id_shop, shop_associations[id_attribute_group]) > -1)
				$(item).attr('disabled', false);
			else
			{
				$(item).attr('disabled', true);
				$(item).attr('checked', false);
			}
		});
	};
	$('#id_attribute_group').change(changeAssociationGroup);
	changeAssociationGroup();
{/block}

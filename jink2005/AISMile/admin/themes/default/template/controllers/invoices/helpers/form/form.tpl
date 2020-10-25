{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{extends file="helpers/form/form.tpl"}

{block name=script}

	$(document).ready(function() {
		var btn_save_date = $('span[class~="process-icon-save-date"]').parent();
		var btn_submit_date = $('#submitPrint');

		if (btn_save_date.length > 0 && btn_submit_date.length > 0)
		{
			btn_submit_date.hide();
			btn_save_date.find('span').removeClass('process-icon-save-date');
			btn_save_date.find('span').addClass('process-icon-save-calendar');
			btn_save_date.click(function() {
				btn_submit_date.before('<input type="hidden" name="'+btn_submit_date.attr("name")+'" value="1" />');

				$('#invoice_date_form').submit();
			});
		}

		var btn_save_status = $('span[class~="process-icon-save-status"]').parent();
		var btn_submit_status = $('#submitPrint2');

		if (btn_save_status.length > 0 && btn_submit_status.length > 0)
		{
			btn_submit_status.hide();
			btn_save_status.find('span').removeClass('process-icon-save-status');
			btn_save_status.find('span').addClass('process-icon-save');
			btn_save_status.click(function() {
				btn_submit_status.before('<input type="hidden" name="'+btn_submit_status.attr("name")+'" value="1" />');

				$('#invoice_status_form').submit();
			});
		}
	});

{/block}

{block name="input"}
	{if $input.type == 'checkboxStatuses'}
		{foreach $input.values.query as $value}
			{assign var=id_checkbox value=$input.name|cat:'_'|cat:intval($value[$input.values.id])}
			<input type="checkbox"
				name="{$input.name}[]"
				id="{$id_checkbox}"
				value="{$value[$input.values.id]|intval}"
				{if isset($fields_value[$id_checkbox]) && $fields_value[$id_checkbox]}checked="checked"{/if} />
			<label for="{$id_checkbox}"
				style="float:none;{if !(isset($statusStats[$value['id_order_state']]) && $statusStats[$value['id_order_state']])}font-weight:normal;{/if}padding:0;text-align:left;width:100%;color:#000">
					<img src="../img/admin/charged_{if $value['invoice']}ok{else}ko{/if}.gif" alt="" />
					{$value[$input.values.name]}
					({if isset($statusStats[$value['id_order_state']]) && $statusStats[$value['id_order_state']]}{$statusStats[$value['id_order_state']]}{else}0{/if})
			</label><br />
		{/foreach}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="after"}<div style="clear:both">&nbsp;</div>{/block}


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
	{if $input.name == 'vat_number'}
		{if $vat == 'is_applicable'}
			<div id="vat_area" style="display: visible">
		{else if $vat == 'management'}
			<div id="vat_area" style="display: hidden">
		{else}
			<div style="display: none;">
		{/if}
	{/if}

	{if $input.type == 'text_customer' && !isset($customer)}
		<label>{l s='Customer e-mail'}</label>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="field"}
	{if $input.type == 'text_customer'}
		{if isset($customer)}
			<div class="margin-form"><a style="display: block; padding-top: 4px;" href="?tab=AdminCustomers&id_customer={$customer->id}&viewcustomer&token={$tokenCustomer}">{$customer->lastname} {$customer->firstname} ({$customer->email})</a></div>
			<input type="hidden" name="id_customer" value="{$customer->id}" />
			<input type="hidden" name="email" value="{$customer->email}" />
		{else}
			<script type="text/javascript">
			$('input[name=email]').live('blur', function(e)
			{
				var email = $(this).val();
				if (email.length > 5)
				{
					var data = {};
					data.email = email;
					data.token = "{$token}";
					data.ajax = 1;
					data.controller = "AdminAddresses";
					data.action = "loadNames";
					$.ajax({
						type: "POST",
						url: "ajax-tab.php",
						data: data,
						dataType: 'json',
						async : true,
						success: function(msg)
						{
							if (msg)
							{
								var infos = msg.infos.split('_');
								$('input[name=firstname]').val(infos[0]);
								$('input[name=lastname]').val(infos[1]);
								$('input[name=company]').val(infos[2]);
							}
						},
						error: function(msg)
						{
						}
					});
				}
			});
			</script>
			<div class="margin-form">
				<input type="text" size="33" name="email" value="{$fields_value[$input.name]|escape:'htmlall':'UTF-8'}" style="text-transform: lowercase;" /> <sup>*</sup>
			</div>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
	{if $input.name == 'vat_number'}
		</div>
	{/if}
{/block}

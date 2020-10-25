{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<form action="{$url_submit}" method="post" id="form_{$list['name_id']}">
	<fieldset>
		<legend><img src="../img/admin/{$list['icon']}.gif" />{$list['title']}</legend>
		<p>{$list['desc']}<p>
		<table cellpadding="0" cellspacing="0" class="table">
			<tr>
				<th style="width: 200px">{$list['title']}</th>
				{foreach $payment_modules as $module}
					{if $module->active}
						<th>
							{if $list['name_id'] != 'currency' || $module->currencies_mode == 'checkbox'}
								<input type="hidden" id="checkedBox_{$list['name_id']}_{$module->name}" value="checked">
								<a href="javascript:checkPaymentBoxes('{$list['name_id']}', '{$module->name}')" style="text-decoration:none;">
							{/if}
							&nbsp;<img src="{$ps_base_uri}modules/{$module->name}/logo.gif" alt="{$module->name}" title="{$module->displayName}" />
							{if $list['name_id'] != 'currency' || $module->currencies_mode == 'checkbox'}
								</a>
							{/if}
						</th>
					{/if}
				{/foreach}
			</tr>
			{foreach $list['items'] as $item}
				<tr class="{cycle values=",alt_row"}">
					<td>{$item['name']}</td>
				{foreach $payment_modules as $key_module => $module}
					{if $module->active}
						<td style="text-align: center">
							{assign var='type' value='null'}
							{if !$item['check_list'][$key_module]}
								{* Keep $type to null *}
							{elseif $list['name_id'] === 'currency'}
								{if $module->currencies && $module->currencies_mode == 'checkbox'}
									{$type = 'checkbox'}
								{elseif $module->currencies && $module->currencies_mode == 'radio'}
									{$type = 'radio'}
								{/if}
							{else}
								{$type = 'checkbox'}
							{/if}
							{if $type != 'null'}
								<input type="checkbox" name="{$module->name}_{$list['name_id']}[]" value="{$item[$list['identifier']]}"
									{if $item['check_list'][$key_module] == 'checked'}checked="checked"{/if} 
								/>
							{else}
								--
							{/if}
						</td>
					{/if}
				{/foreach}
				</tr>
			{/foreach}
			{if $list['name_id'] === 'currency'}
				<tr class="{cycle values=",alt_row"}">
					<td>{l s='Customer currency'}</td>
					{foreach $payment_modules as $module}
						{if $module->active}
							<td style="text-align: center">{if $module->currencies && $module->currencies_mode == 'radio'}<input type="radio" name="{$module->name}_{$list['name_id']}[]" value="-1"{if in_array(-1, $module->$list['name_id'])} checked="checked"{/if} />{else}--{/if}</td>
						{/if}
					{/foreach}
				</tr>
				<tr class="{cycle values=",alt_row"}">
					<td>{l s='Shop default currency'}</td>
					{foreach $payment_modules as $module}
						{if $module->active}
							<td style="text-align: center">{if $module->currencies && $module->currencies_mode == 'radio'}<input type="radio" name="{$module->name}_{$list['name_id']}[]" value="-2"{if in_array(-2, $module->$list['name_id'])} checked="checked"{/if} />{else}--{/if}</td>
						{/if}
					{/foreach}
				</tr>
			{/if}
		</table>
		<div><input type="submit" class="button space" name="submitModule{$list['name_id']}" value="{l s='Save restrictions'}" /></div>
	</fieldset>
</form>
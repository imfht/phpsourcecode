{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{extends file="helpers/form/form.tpl"}

{block name="input"}
	{if $input.type == 'file'}
		{if isset($input.display_image) && $input.display_image}
			{if isset($fields_value.image) && $fields_value.image}
				<div id="image">
					{$fields_value.image}
					<p align="center">{l s='File size'} {$fields_value.size}kb</p>
					{if $shared_category}
						<p class="warn">{l s='If you delete this picture it\'s will be deleted for all shared shop'}</p>
					{/if}
					<br>
					<a href="{$current}&{$identifier}={$form_id}&token={$token}&{if $shared_category}forcedeleteImage=1{else}deleteImage=1{/if}">
						<img src="../img/admin/delete.gif" alt="{l s='Delete'}" /> {l s='Delete'}
					</a>
				</div><br />
			{/if}
		{/if}
		<input type="file" name="{$input.name}" {if isset($input.id)}id="{$input.id}"{/if} />
		{if !empty($input.hint)}<span class="hint" name="help_box">{$input.hint}<span class="hint-pointer">&nbsp;</span></span>{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
{block name="description"}
	{$smarty.block.parent}
	{if ($input.name == 'groupBox')}
		<p class="hint" style="display:block; position:relative; display:inline-block;">
			<span>{$input.info_introduction}</span><br />
			<span>{$input.unidentified}</span><br />
			<span>{$input.guest}</span><br />
			<span>{$input.customer}</span><br />
		</p>
	{/if}
{/block}

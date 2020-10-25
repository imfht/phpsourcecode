{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/options/options.tpl"}
{block name="input"}
	{if $field['type'] == 'rewriting_settings'}
			<label class="t" for="{$key}_on"><img src="../img/admin/enabled.gif" alt="{l s='Yes'}" title="{l s='Yes'}" /></label>
			<input type="radio" name="{$key}" id="{$key}_on" value="1" {if $field['value']} checked="checked"{/if}{if isset($field['js']['on'])} {$field['js']['on']}{/if}/>
			<label class="t" for="{$key}_on"> {l s='Yes'}</label>
			<label class="t" for="{$key}_off"><img src="../img/admin/disabled.gif" alt="{l s='No'}" title="{l s='No'}" style="margin-left: 10px;" /></label>
			<input type="radio" name="{$key}" id="{$key}_off" value="0" {if !$field['value']} checked="checked"{/if}{if isset($field['js']['off'])} {$field['js']['off']}{/if}/>
			<label class="t" for="{$key}_off"> {l s='No'}</label>
		{if !$field['mod_rewrite']}
			<span class="warning_mod_rewrite">{l s='URL rewriting (mod_rewrite) is not active on your server or it is not possible to check your server configuration. If you want to use Friendly URLs you must activate this mod.'}</span>
			<div class="clear"></div>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}


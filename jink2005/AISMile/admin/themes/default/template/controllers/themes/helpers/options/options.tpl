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
	{if $field['type'] == 'theme'}
		{if $field['can_display_themes']}
			{foreach $field.themes as $theme}
				<div class="select_theme {if $theme->id == $field['id_theme']}select_theme_choice{/if}" onclick="$(this).find('input').attr('checked', true); $('.select_theme').removeClass('select_theme_choice'); $(this).toggleClass('select_theme_choice');">
					{$theme->name}<br />
					<img src="../themes/{$theme->directory}/preview.jpg" alt="{$theme->directory}" /><br />
					<input type="radio" name="id_theme" value="{$theme->id}" {if $theme->id == $field['id_theme']}checked="checked"{/if} />
				</div>
			{/foreach}
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
{/block}

{block name="after"}
	<br/><br/>
	<fieldset id="prestastore-content" class="width3"></fieldset>
	<script type="text/javascript">
		$.post(
			"ajax-tab.php",
			{
				tab: 'AdminThemes',
				token: '{$token}',
				ajax: '1',
				action:'getAddonsThemes',
				page:'themes'
			}, function(a){
				$("#prestastore-content").html("<legend><img src='../img/admin/prestastore.gif' class='middle' />{l s='Live from MileBiz Addons!'}</legend>"+a);
			});
	</script>
{/block}

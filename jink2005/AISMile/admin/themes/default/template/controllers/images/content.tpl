{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{$content}

{if isset($display_regenerate)}
	<h2 class="space">{l s='Regenerate thumbnails'}</h2>
	{l s='Regenerates thumbnails for all existing images'}<br /><br />
	<div  class="width4">
		<div class="warn">
			{l s='Please be patient, this can take several minutes'}<br />
			{l s='Be careful! Manually uploaded thumbnails will be erased and replaced by automatically generated thumbnails.'}
		</div>
	</div>
	<form action="{$current}&token={$token}" method="post">
		<fieldset class="width4">
			<legend><img src="../img/admin/picture.gif" /> {l s='Regenerate thumbnails'}</legend><br />
			<label>{l s='Select image'}</label>
			<div class="margin-form">
				<select name="type" onchange="changeFormat(this)">
					<option value="all">{l s='All'}</option>
					{foreach $types AS $k => $type}
						<option value="{$k}">{$type}</option>
					{/foreach}
				</select>
			</div>

			{foreach $types AS $k => $type}
				<label class="second-select format_{$k}" style="display:none;">{l s='Select format'}</label>
				<div class="second-select margin-form format_{$k}" style="display:none;">
					<select class="second-select format_{$k}" name="format_{$k}">
						<option value="all">{l s='All'}</option>
						{foreach $formats[$k] AS $format}
							<option value="{$format['id_image_type']}">{$format['name']}</option>
						{/foreach}
					</select>
				</div>
			{/foreach}
			<script>
				function changeFormat(elt)
				{ldelim}
					$('.second-select').hide();
					$('.format_' + $(elt).val()).show();
				{rdelim}
			</script>
			<label>{l s='Erase previous images'}</label>
			<div class="margin-form">
				<input name="erase" type="checkbox" value="1" checked="checked" />
				<p>{l s='Deselect this checkbox only if your server timed out and you need to resume the regeneration.'}</p>
			</div>
			<div class="clear"></div>
			<center><input type="Submit" name="submitRegenerate{$table}" value="{l s='Regenerate thumbnails'}" class="button space" onclick="return confirm('{l s='Are you sure?'}');" /></center>
		</fieldset>
	</form>
{/if}

{if isset($display_move)}
	<br /><h2 class="space">{l s='Move images'}</h2>
	{l s='MileBiz now uses a new storage system for product images. It offers better performance if your shop has a very large number of products.'}<br />
	<br />
	{if $safe_mode}
		<div class="warn">
			{l s='MileBiz has detected that your server configuration is not compatible with the new storage system (directive "safe_mode" is activated). You should continue to use the existing system.'}
		</div>
	{else}
		<form action="{$current}&token={$token}" method="post">
			<fieldset class="width4">
				<legend><img src="../img/admin/picture.gif" /> {l s='Move images'}</legend><br />
				{l s='You can choose to keep your images stored in the previous system - there is nothing wrong with that.'}<br />
				{l s='You can also decide to move your images to the new storage system: in this case, click on the "Move images" button below. Please be patient, this can take several minutes.'}
				<br /><br />
				<div class="hint clear" style="display: block;">&nbsp;
					{l s='After moving all your product images, for best performance, set the "Use the legacy image filesystem" option above to "No".'}
				</div>
				<center><input type="Submit" name="submitMoveImages{$table}" value="{l s='Move images'}" class="button space" onclick="return confirm('{l s='Are you sure?'}');" /></center>
			</fieldset>
		</form>
	{/if}
{/if}
{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<br/>
<div class="blocEngine">
	<form action="{$current}&token={$token}" method="post" id="settings_form" name="settings_form">
		<h3 class="icon-{l s='Settings'}">{l s='Settings'}</h3>

		<div class="rowForm">
			<label for="engine_stats_render">{l s='Graph engine'} </label>
			{if count($array_graph_engines)}
				<select name="PS_STATS_RENDER" id="engine_stats_render">
					{foreach $array_graph_engines as $k => $value}
						<option value="{$k}" {if $k == $graph_engine}selected="selected"{/if}>{$value[0]}</option>
					{/foreach}
				</select>

			{else}
				{l s='No graph engine module installed'}
			{/if}
		</div>

		<div class="rowForm">
			<label for="engine_grid_render">{l s='Grid engine'} </label>
			{if count($array_grid_engines)}
				<select name="PS_STATS_GRID_RENDER" id="engine_grid_render">
					{foreach $array_grid_engines as $k => $value}
						<option value="{$k}" {if $k == $grid_engine}selected="selected"{/if}>{$value[0]}</option>
					{/foreach}
				</select>
			{else}
				{l s='No grid engine module installed'}
			{/if}
		</div>

		<div class="rowForm">
			<label for="engine_auto_clean">{l s='Auto-clean period'}</label>
			<select name="PS_STATS_OLD_CONNECT_AUTO_CLEAN" id="engine_auto_clean">
				{foreach $array_auto_clean as $k => $value}
					<option value="{$k}" {if $k == $auto_clean}selected="selected"{/if}>{$value}</option>
				{/foreach}
			</select>
		</div>
		<p><input type="submit" value="{l s='Save'}" name="submitSettings" id="submitSettings" class="button" /></p>
</form>
</div>

</div>
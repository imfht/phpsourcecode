{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
<tbody>
{foreach $list AS $index => $tr}
	<tr
	{if $position_identifier}id="tr_{$id_category}_{$tr.$identifier}_{$tr.position['position']}"{/if}
	{if $index is odd}class="alt_row"{/if}
	{if isset($tr.color) && $color_on_bg}style="background-color: {$tr.color}"{/if}
	>
		<td class="center">
			{if $bulk_actions}
				{assign var=bulkActionPossible value=true}
				{foreach $list_skip_actions as $key => $value}
					{if in_array($tr.$identifier, $value) == true}
						{assign var=bulkActionPossible value=false}
					{/if}
				{/foreach}
				{if $bulkActionPossible == true}
					<input type="checkbox" name="{$table}Box[]" value="{$tr.$identifier}" class="noborder" />
				{/if}
			{/if}
		</td>
		{foreach $fields_display AS $key => $params}
			<td
				{if isset($params.position)}
					id="td_{if $id_category}{$id_category}{else}0{/if}_{$tr.$identifier}"
				{/if}
				class="{if !$no_link}pointer{/if}
					   {if isset($params.position) && $order_by == 'position'} dragHandle{/if}
					   {if isset($params.align)} {$params.align}{/if}"

			{if (!isset($params.position) && !$no_link)}
				onclick="document.location = '{$current_index}&{$identifier}={$tr.$identifier}{if $view}&view{else}&update{/if}{$table}&token={$token}'">{if isset($params.prefix)}{$params.prefix}{/if}
			{else}
				>
			{/if}
			{if isset($params.active)}
			    {$tr.$key}
			{elseif isset($params.activeVisu)}
				<img src="../img/admin/{if $tr.$key}enabled.gif{else}disabled.gif{/if}"
				alt="{if $tr.$key}{l s='Enabled'}{else}{l s='Disabled'}{/if}" title="{if $tr.$key}{l s='Enabled'}{else}{l s='Disabled'}{/if}" />
			{elseif isset($params.position)}
				{if $order_by == 'position' && $order_way != 'DESC'}
					<a href="{$tr.$key.position_url_down}" {if !($tr.$key.position != $positions[count($positions) - 1])}style="display: none;"{/if}>
						<img src="../img/admin/{if $order_way == 'ASC'}down{else}up{/if}.gif" alt="{l s='Down'}" title="{l s='Down'}" />
					</a>

					<a href="{$tr.$key.position_url_up}" {if !($tr.$key.position != $positions.0)}style="display: none;"{/if}>
						<img src="../img/admin/{if $order_way == 'ASC'}up{else}down{/if}.gif" alt="{l s='Up'}" title="{l s='Up'}" />
					</a>
				{else}
					{$tr.$key.position + 1}
				{/if}
			{elseif isset($params.image)}
				{$tr.$key}
			{elseif (isset($params.icon))}
				<img src="../img/admin/{$tr[$key]}" alt="{$tr[$key]}" title="{$tr[$key]}" />
            {elseif isset($params.price)}
				{$tr.$key}
			{elseif isset($params.float)}
				{$tr.$key}
			{elseif isset($params.type) && $params.type == 'date'}
				{$tr.$key}
			{elseif isset($params.type) && $params.type == 'datetime'}
				{$tr.$key}
			{elseif isset($params.callback)}
				{$tr.$key}
			{elseif isset($tr.$key)}
				{if $key == 'behavior'}
					{if $tr.$key == 0}
						{l s='This tax only'}
					{elseif $tr.$key == 1}
						{l s='Compute with others'}
					{elseif $tr.$key == 2}
						{l s='One after another'}
					{/if}
				{elseif $key == 'rate'}
					{$tr.$key|string_format:"%.2f"}%
				{elseif $key == 'zipcode'}
					{if $tr.$key == '0 - 0'}
						--
					{else}
						{$tr.$key|escape:'htmlall':'UTF-8'}
					{/if}
				{else}
					{$tr.$key|escape:'htmlall':'UTF-8'}
				{/if}
			{else}
				--
			{/if}
			{if isset($params.suffix)}{$params.suffix}{/if}
			</td>
		{/foreach}

	{if $shop_link_type}
		<td class="center" {if $name != $tr.shop_name}title="{$tr.shop_name}"{/if}>{if isset($tr.shop_short_name)}{$tr.shop_short_name}{else}{$tr.shop_name}{/if}</td>
	{/if}
	{if $has_actions}
		<td class="center" style="white-space: nowrap;">
			{foreach $actions AS $action}
				{if isset($tr.$action)}
	            	{$tr.$action}
	            {/if}
			{/foreach}
		</td>
	{/if}
	</tr>
{/foreach}
</tbody>
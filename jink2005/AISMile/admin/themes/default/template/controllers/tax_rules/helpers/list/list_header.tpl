{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{hook h='displayAdminListBefore'}
{if isset($name_controller)}
	{capture name=hookName assign=hookName}display{$name_controller|ucfirst}ListBefore{/capture}
	{hook h=$hookName}
{elseif isset($smarty.get.controller)}
	{capture name=hookName assign=hookName}display{$smarty.get.controller|ucfirst|htmlentities}ListBefore{/capture}
	{hook h=$hookName}
{/if}



<form method="post" action="{$action}" class="form">

	<table class="table_grid">
		<tr>
			<td>
				<table
				{if $table_id} id={$table_id}{/if}
				class="table {if $table_dnd}tableDnD{/if} {$table}"
				cellpadding="0" cellspacing="0"
				style="width: 100%; margin-bottom:10px;"
				>
					<col width="10px" />
					{foreach $fields_display AS $key => $params}
						<col {if isset($params.width) && $params.width != 'auto'}width="{$params.width}px"{/if}/>
					{/foreach}
					{if $shop_link_type}
						<col width="80px" />
					{/if}
					{if $has_actions}
						<col width="52px" />
					{/if}
					<thead>
						<tr class="nodrag nodrop">
							<th class="center">
								{if $has_bulk_actions}
									<input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, '{$table}Box[]', this.checked)" />
								{/if}
							</th>
							{foreach $fields_display AS $key => $params}
								<th {if isset($params.align)} class="{$params.align}"{/if}>
									{if isset($params.hint)}<span class="hint" name="help_box">{$params.hint}<span class="hint-pointer">&nbsp;</span></span>{/if}
									<span class="title_box">
										{$params.title}
									</span>
										<br />&nbsp;
								</th>
							{/foreach}
							{if $shop_link_type}
								<th>
									{if $shop_link_type == 'shop'}
										{l s='Shop'}
									{else}
										{l s='Group shop'}
									{/if}
									<br />&nbsp;
								</th>
							{/if}
							{if $has_actions}
								<th class="center">{l s='Actions'}<br />&nbsp;</th>
							{/if}
						</tr>
						</thead>

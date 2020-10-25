{include file="header.tpl" title="Login"}
<body class="list">
<div id="topbar">
	<div id="leftnav"><a href="index.php"><img alt="home" src="templates/{$settings.template}/images/home.png" /></a><a href="manageproject.php?action=showproject&id={$project.ID}">{$projectname|truncate:32:"...":true}</a></div>
	<div id="rightnav"><a href="manageuser.php?action=logout">{#logout#}</a></div>
</div>

<div id="content">
	<ul>
		<li class="title">{$projectname|truncate:16:"...":true} / {#tasklists#}</li>
{if $lists[0][0]}
	{section name=list loop=$lists}
		<li class="withimage">
			<a class="noeffect" href="managetasklist.php?action=showtasklist&amp;id={$project.ID}&amp;tlid={$lists[list].ID}">
				<div class="wp">
				{if $lists[list].is_started}
					<div class="{if $lists[list].timeok}ok{else}no{/if}">Time</div>
					<div class="{if $lists[list].forecast<$lists[list].planeffort}ok{else}no{/if}">Cost</div>
				{else}
					<div class="non">Opened</div>
				{/if}
				</div>
				<span class="name">{$lists[list].name}</span>
				<span class="comment">
					{"d.m"|date:$lists[list].startdate} - {"d.m.Y"|date:$lists[list].finishdate}<br />
					<span class="colleft">{#done#}: {$lists[list].done}%<br />{#plan#}: {$lists[list].planeffort}h</span>
					<span class="colright">{#actualeffort#}: {$lists[list].actual}<br />{#forecast#}: {$lists[list].forecast}h</span>
				</span>
				<span class="arrow"></span>
			</a>
		</li>
	{/section}
{else}
	<li class="textbox">{#notasklists#}</li>
{/if}
	</ul>
</div>
{include file="footer.tpl"}
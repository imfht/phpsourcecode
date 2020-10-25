{include file="header.tpl" title="Login"}
<body class="list">
<div id="topbar">
	<div id="leftnav"><a href="index.php"><img alt="home" src="templates/{$settings.template}/images/home.png" /></a><a href="manageproject.php?action=showproject&id={$project.ID}">{$projectname|truncate:32:"...":true}</a></div>
	<div id="rightnav"><a href="manageuser.php?action=logout">{#logout#}</a></div>
</div>

<div id="content">
	<ul>
		<li class="title">{$projectname|truncate:16:"...":true} / {#milestones#}</li>
{if count($milestones)>0}
	{section name=stone loop=$milestones}
		<li class="withimage">
			<a class="noeffect" href="managemilestone.php?action=showmilestone&amp;msid={$milestones[stone].ID}&amp;id={$project.ID}">
				<div class="mile">
					{if $milestones[stone].external}<div class="external">&nbsp;</div>External{else}
					<div class="internal">&nbsp;</div>Internal{/if}
				</div>
				<span class="name">{$milestones[stone].name|truncate:30:"...":true}</span>
				<span class="comment">{#due#}: {$milestones[stone].fend}<br />{#daysleft#}: {$milestones[stone].dayslate}</span>
				<span class="arrow"></span>
			</a>
		</li>
	{/section}
{/if}
	</ul>
</div>
{include file="footer.tpl"}
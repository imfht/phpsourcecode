{include file="header.tpl" title="Login"}
<body class="list">
<div id="topbar">
	<div id="leftnav"><a href="index.php"><img alt="home" src="templates/{$settings.template}/images/home.png" /></a><a href="manageproject.php?action=showproject&id={$project.ID}">{$projectname|truncate:32:"...":true}</a></div>
	<div id="rightnav"><a href="manageuser.php?action=logout">{#logout#}</a></div>
</div>

<div id="content">
	<ul>
		<li class="title">{$projectname|truncate:16:"...":true} / {#tasks#}</li>
{if $lists[0][0]}
	{section name=list loop=$lists}
		{section name=task loop=$lists[list].tasks}
		<li class="withimage">
			<a class="noeffect" href="managetask.php?action=showtask&amp;tid={$lists[list].tasks[task].ID}&amp;id={$lists[list].tasks[task].project}">
				<img src="templates/{$settings.template}/images/workpackage.png" />
				<span class="name">{if $lists[list].tasks[task].title != ""}{$lists[list].tasks[task].title|truncate:30:"...":true}{else}{$lists[list].tasks[task].text|truncate:30:"...":true}{/if}</span>
				<span class="comment">
					{#user#}: {section name=theusers loop=$lists[list].tasks[task].users}{$lists[list].tasks[task].users[theusers].name|truncate:30:"...":true} {/section}<br />
					{#priority#}: {$lists[list].tasks[task].priority_name}<br />
					{#effort#} ({#actualeffort#}): {$lists[list].tasks[task].efforttocomplete} ({$lists[list].tasks[task].actual})<br />
				</span>
				<span class="arrow"></span>
			</a>
		</li>
		{/section}
	{/section}
{/if}
	</ul>
</div>
{include file="footer.tpl"}
{include file="header.tpl" title="Login"}
<body class="list">
<div id="topbar">
	<div id="leftnav"><a href="index.php"><img alt="home" src="templates/{$settings.template}/images/home.png" /></a></div>
	<div id="rightnav"><a href="manageuser.php?action=logout">{#logout#}</a></div>
</div>

<div id="content">
	<ul>
		<li class="title">{#mytasks#}</li>
{section name=project loop=$myprojects}
	{if $myprojects[project].tasknum > 0}
		{section name=task loop=$myprojects[project].tasks}
		<li class="withimage">
			<a class="noeffect" href="managetask.php?action=showtask&amp;tid={$myprojects[project].tasks[task].ID}&amp;id={$myprojects[project].tasks[task].project}">
				<img src="templates/{$settings.template}/images/workpackage.png" />
				<span class="name">{if $myprojects[project].tasks[task].title != ""}{$myprojects[project].tasks[task].title|truncate:30:"...":true}{else}{$myprojects[project].tasks[task].text|truncate:30:"...":true}{/if}</span>
				<span class="comment">
					{#tasklist#}: {$myprojects[project].tasks[task].list|truncate:23:"...":true}<br />
					{#priority#}: {$myprojects[project].tasks[task].priority_name}<br />
					{#daysleft#}: {$myprojects[project].tasks[task].daysleft}
				</span>
				<span class="arrow"></span>
			</a>
		</li>
		{/section}
	{/if}
{/section}
	</ul>
</div>
{include file="footer.tpl"}
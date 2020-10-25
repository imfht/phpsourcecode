{include file="header.tpl" title="Login"}
<body>
<div id="topbar">
	<div id="rightnav"><a href="/{$settings.template2}/manageuser.php?action=logout">{#logout#}</a></div>
</div>

<div id="content">
	<ul class="pageitem">
{if $projectnum > 0}
	{section name=project loop=$myprojects}
		<li class="menu">
			<a class="noeffect" href="/{$settings.template2}/manageproject.php?action=showproject&id={$myprojects[project].ID}">
				<img src="templates/{$settings.template}/images/project.png" />
				<span class="name">{if $myprojects[project].name != ""}{$myprojects[project].name|truncate:30:"...":true}{else}{$myprojects[project].desc|truncate:30:"...":true}{/if}</span>
				<span class="arrow"></span>
			</a>
		</li>
	{/section}
{/if}
		<li class="menu">
			<a class="noeffect" href="/{$settings.template2}/manageproject.php?action=calendar"><img alt="itunes" src="templates/{$settings.template}/images/calendar.png" /><span class="name">{#calendar#}</span><span class="arrow"></span></a>
		</li>
		<li class="menu">
			<a class="noeffect" href="/{$settings.template2}/mytasks.php"><img alt="itunes" src="templates/{$settings.template}/images/task.png" /><span class="name">{#mytasks#}</span><span class="comment">({$tasknum})</span><span class="arrow"></span></a>
		</li>
		<li class="menu">
			<a class="noeffect" href="/{$settings.template2}/managemessage.php?action=mymsgs"><img alt="itunes" src="templates/{$settings.template}/images/mail.png" /><span class="name">{#mymessages#}</span><span class="comment">({$msgnum})</span><span class="arrow"></span></a>
		</li>
	</ul>
</div>
{include file="footer.tpl"}
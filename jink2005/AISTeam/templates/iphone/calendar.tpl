{include file="header.tpl" title="Login"}
<body class="list">
<div id="topbar" style="margin-bottom:0;">
	<div id="leftnav"><a href="index.php"><img alt="home" src="templates/{$settings.template}/images/home.png" /></a></div>
	<div id="rightnav"><a href="manageuser.php?action=logout">{#logout#}</a></div>
</div>
<div id="topbar">
	<div id="leftnav"><a href="manageproject.php?action=calendar&date={$date.prev}">Previous Week</a></div>
	<div id="rightnav"><a href="manageproject.php?action=calendar&date={$date.next}">Next Week</a></div>
</div>

<div id="content">
	<ul>
{foreach from=$events key=date item=event}
		<li class="title">{"l, d F Y"|date:$date}</li>
	{foreach from=$event.milestones item=milestone}
		<li class="withimage small">
			<a class="noeffect" href="managemilestone.php?action=showmilestone&msid={$milestone.ID}&id={$milestone.project}&view=calendar">
				<img src="templates/standard/images/symbols/miles.png" />
				<span class="name">{$milestone.name|truncate:30:"...":true}</span>
				<span class="arrow"></span>
			</a>
		</li>
	{/foreach}
	{foreach from=$event.tasks item=task}
		<li class="withimage small">
			<a class="noeffect" href="managetask.php?action=showtask&tid={$task.ID}&id={$task.project}&view=calendar">
				<img src="templates/standard/images/symbols/task.png" />
				<span class="name">{$task.title|truncate:30:"...":true}</span>
				<span class="arrow"></span>
			</a>
		</li>
	{/foreach}
{/foreach}
	</ul>
</div>
{include file="footer.tpl"}

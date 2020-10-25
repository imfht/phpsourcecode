{include file="header.tpl" title="Login"}
<body>
<div id="topbar">
	<div id="leftnav"><a href="index.php"><img alt="home" src="templates/{$settings.template2}/images/home.png" /></a></div>
	{*<div id="title">{$settings.name}</div>*}
	<div id="rightnav"><a href="manageuser.php?action=logout">{#logout#}</a></div>
</div>

<div id="content">
	<ul class="pageitem">
		<li class="menu">
			<a class="noeffect" href="managetask.php?action=showproject&id={$project.ID}"><img alt="itunes" src="templates/{$settings.template}/images/project.png" /><span class="name">{#tasklists#}</span><span class="comment">({$workpackagenum})</span><span class="arrow"></span></a>
		</li>
		<li class="menu">
			<a class="noeffect" href="managemilestone.php?action=showproject&id={$project.ID}"><img alt="itunes" src="templates/{$settings.template}/images/milestone.png" /><span class="name">{#milestones#}</span><span class="comment">({$milestonenum})</span><span class="arrow"></span></a>
		</li>
		<li class="menu">
			<a class="noeffect" href="managetask.php?action=showprojecttasks&id={$project.ID}"><img alt="itunes" src="templates/{$settings.template}/images/task.png" /><span class="name">{#tasks#}</span><span class="comment">({$tasknum})</span><span class="arrow"></span></a>
		</li>
		<li class="menu">
			<a class="noeffect" href="manageproject.php?action=showactivities&id={$project.ID}"><img alt="itunes" src="templates/{$settings.template}/images/activity.png" /><span class="name">{#activity#}</span><span class="arrow"></span></a>
		</li>
		<li class="menu">
			<a class="noeffect" href="managetimetracker.php?action=showproject&id={$project.ID}"><img alt="itunes" src="templates/{$settings.template}/images/time.png" /><span class="name">{#timetracker#}</span><span class="arrow"></span></a>
		</li>
	</ul>
</div>
{include file="footer.tpl"}
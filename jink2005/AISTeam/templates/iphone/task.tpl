{include file="header.tpl" title="Login"}
<body>
<div id="topbar">
	<div id="leftnav">
		<a href="index.php"><img alt="home" src="templates/{$settings.template}/images/home.png" /></a>
{if $smarty.get.view=="calendar"}
		<a href="manageproject.php?action=calendar">{#calendar#}</a>
{else}
		<a href="manageproject.php?action=showproject&id={$project.ID}">{$projectname|truncate:16:"...":true}</a>
		<a href="managetask.php?action=showprojecttasks&id={$project.ID}">{#tasks#}</a>
{/if}
	</div>
	<div id="rightnav"><a href="manageuser.php?action=logout">{#logout#}</a></div>
</div>

<div id="content">
	<ul class="pageitem">
		<li class="textbox"><span class="header">{$task.title|truncate:40:"...":true}</span>{$task.text}</li>
	</ul>
	<ul class="pageitem">
		<li class="smallfield"><span class="name">{#user#}</span><span class="info">{$task.user|truncate:25:"...":true}</span></li>
		<li class="smallfield"><span class="name">{#end#}</span><span class="info">{$task.endstring}</span></li>
		<li class="smallfield"><span class="name">{#effort#}</span><span class="info">{$task.efforttocomplete}</span></li>
		<li class="smallfield"><span class="name">{#actualeffort#}</span><span class="info">{$task.actual}</span></li>
		<li class="smallfield"><span class="name">{#priority#}</span><span class="info">{$task.priority_name}</span></li>
		<li class="smallfield"><span class="name">{#tasklist#}</span><span class="info">{section name=tasklist loop=$tasklists}{if $task.listid == $tasklists[tasklist].ID}{$tasklists[tasklist].name}{/if}{/section}</span></li>
	</ul>
</div>
{include file="footer.tpl"}
{include file="header.tpl" title="Login"}
<body>
<div id="topbar">
	<div id="leftnav">
		<a href="index.php"><img alt="home" src="templates/{$settings.template}/images/home.png" /></a>
{if $smarty.get.view=="calendar"}
		<a href="manageproject.php?action=calendar">{#calendar#}</a>
{else}
		<a href="manageproject.php?action=showproject&id={$project.ID}">{$projectname|truncate:16:"...":true}</a>
		<a href="managemilestone.php?action=showproject&id={$project.ID}">{#milestones#}</a>
{/if}
	</div>
	<div id="rightnav"><a href="manageuser.php?action=logout">{#logout#}</a></div>
</div>

<div id="content">
	<ul class="pageitem">
		<li class="textbox"><span class="header">{$milestone.name}</span>{$milestone.desc}</li>
	</ul>
	<ul class="pageitem">
		<li class="smallfield"><span class="name">{#date#}</span><span class="info">{$milestone.endstring}</span></li>
	</ul>
</div>
{include file="footer.tpl"}

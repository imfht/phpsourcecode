{include file="header.tpl" title="Login"}
<body class="list">
<div id="topbar">
	<div id="leftnav"><a href="index.php"><img alt="home" src="templates/{$settings.template}/images/home.png" /></a><a href="manageproject.php?action=showproject&id={$project.ID}">{$project.name|truncate:32:"...":true}</a></div>
	<div id="rightnav"><a href="manageuser.php?action=logout">{#logout#}</a></div>
</div>

<div id="content">
	<ul>
		<li class="title">{$project.name|truncate:16:"...":true} / {#activity#}</li>
{section name=logitem loop=$log}
		<li class="withimage">
			<a class="noeffect" href="" onclick="return false">
				{if $log[logitem].type == "tasklist"}
				<img src="templates/standard/images/symbols/tasklist.png" />
				{elseif $log[logitem].type == "user"}
				<img src="templates/standard/images/symbols/user.png" />
				{elseif $log[logitem].type == "task"}
				<img src="templates/standard/images/symbols/task.png" />
				{elseif $log[logitem].type == "projekt"}
				<img src="templates/standard/images/symbols/projects.png" />
				{elseif $log[logitem].type == "milestone"}
				<img src="templates/standard/images/symbols/miles.png" />
				{elseif $log[logitem].type == "message"}
				<img src="templates/standard/images/symbols/msgs.png" />
				{elseif $log[logitem].type == "datei"}
				<img src="templates/standard/images/symbols/files.png" />
				{elseif $log[logitem].type == "track"}
				<img src="templates/standard/images/symbols/timetracker.png" />
				{/if}
				<span class="name">{$log[logitem].name|truncate:30:"...":true}
				</span>
				<span class="comment">{#was#}
					{if $log[logitem].action == 1}{#added#}
					{elseif $log[logitem].action == 2}{#edited#}
					{elseif $log[logitem].action == 3}{#deleted#}
					{elseif $log[logitem].action == 4}{#opened#}
					{elseif $log[logitem].action == 5}{#closed#}
					{elseif $log[logitem].action == 6}{#assigned#}{/if}
					{$log[logitem].datum}<br />
					{#user#}: {$log[logitem].username|truncate:25:"...":true}
				</span>
			</a>
		</li>
{/section}
	</ul>
</div>
{include file="footer.tpl"}
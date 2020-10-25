{include file="header.tpl" title="Login"}
<body>
<div id="topbar">
	<div id="leftnav"><a href="index.php"><img alt="home" src="templates/{$settings.template}/images/home.png" /></a></div>
	<div id="rightnav"><a href="manageuser.php?action=logout">{#logout#}</a></div>
</div>

<div id="content">
	<span class="graytitle">{#mymessages#}</span> 
	<ul class="pageitem">
{section name= project loop=$myprojects}
	{section name=message loop=$myprojects[project].messages}
		<li class="store">
			<a class="noeffect" href="managemessage.php?action=editform&amp;mid={$myprojects[project].messages[message].ID}&amp;id={$myprojects[project].ID}">
				<span class="name">{$myprojects[project].messages[message].title|truncate:35:"...":true} <span class="normal">({$myprojects[project].messages[message].username|truncate:20:"...":true})</span></span>
				<span class="comment text">
					{$myprojects[project].messages[message].text|truncate:150:"..."}
				</span>
				<span class="arrow"></span>
			</a>
		</li>
	{/section}
{/section}
	</ul>
</div>
{include file="footer.tpl"}
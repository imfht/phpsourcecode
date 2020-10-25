{include file="header.tpl" title="Login"}
<body>
<div id="topbar">
	<div id="leftnav"><a href="index.php"><img alt="home" src="templates/{$settings.template}/images/home.png" /></a></div>
	<div id="rightnav"><a href="manageuser.php?action=logout">{#logout#}</a></div>
</div>

<div id="content">
	<ul class="pageitem">
		<li class="textbox error"><span class="header">{#error#}</span>{$errortext}</li>
	</ul>
</div>
{include file="footer.tpl"}
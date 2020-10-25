{include file="header.tpl" title="Login"}
<body>
<div id="topbar">
	<div id="title">{$settings.name} {$settings.subtitle}</div>
</div>

<div id="content">
	<form id="loginform" name="loginform" method="post" action="/{$settings.template2}/manageuser.php?action=login" onsubmit="return validateCompleteForm(this,'input_error')">
		<ul class="pageitem">
			<li class="bigfield"><input placeholder="{#name#}" type="text" name="username" id="username" required="1" realname="{#name#}" /></li>
			<li class="bigfield"><input placeholder="{#password#}" type="password" name="pass" id="pass" realname="{#password#}" /></li>
			<li class="checkbox"><span class="name">{#stayloggedin#}</span><input type="checkbox" name="staylogged" id="stay" value="1" /></li>
			<li class="button"><input type="submit" value="{#loginbutton#}" /></li>
		</ul>
		{if $loginerror == 1}<ul class="pageitem"><li class="textbox error">{#loginerror#}</li></ul>{/if}
	</form>
</div>
{include file="footer.tpl"}
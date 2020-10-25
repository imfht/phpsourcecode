{include file="header.tpl" title="Login" showheader="no" jsload = "ajax"}


<div class="login">
	<div class="login-in">
		<div class="logo-name">
			<h1><a href = "http://2-plan.com/" title = "{$settings.name} Open Source project management"><img src="./templates/standard/images/logo-a.png" alt="{$settings.name}"  /></a></h1>
			<h2>{$settings.subtitle}</h2>
		</div>

		<form id = "loginform" name = "loginform" method="post" action="manageuser.php?action=login" {literal} onsubmit="return validateCompleteForm(this,'input_error');"{/literal}>
			<fieldset>

			<div class="row">
				<label for="username" class="username">{#login#} ({#email#}):</label>
				<input type="text" class="text" name="username" id="username" required = "1" realname = "{#login#} ({#email#})" />
			</div>

			<div class="row">
				<label for="pass" class="pass">{#password#}:</label>
				<input type="password" class="text" name="pass" id="pass" realname = "{#password#}" />
			</div>

			<div class="row">
				<label class="right">
					<input type="checkbox" name="staylogged" value="1" /> remember me for 2 weeks
				</label>
			</div>

			<div class="row">
				<label class="right">
					<a href="manageuser.php?action=forgot" class="forgot" onclick="location.href='manageuser.php?action=forgot';return false;">forgot password?</a>
					<input type="submit" value="{#loginbutton#}" class="loginbutn" />
				</label>
			</div>
			</fieldset>
		</form>
	</div>


	{if $loginerror == 1}<div class="login-alert">{#loginerror#}</div>{/if}
	{if $loginerrormessage !=""}<div class="login-alert">{$loginerrormessage}</div>{/if}
	{if $loginmessage !=""}<div class="login-message">{$loginmessage}</div>{/if}
</div>



</body>
</html>

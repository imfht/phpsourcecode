{include file="header.tpl" title="Login" showheader="no" jsload = "ajax"}


<div class="login">
	<div class="login-in">
		<div class="logo-name">
			<h1><a href = "http://2-plan.o-dyn.de/" title = "{$settings.name} Open Source project management"><img src="./templates/standard/images/logo-a.png" alt="{$settings.name}"  /></a></h1>
			<h2>{$settings.subtitle}</h2>
		</div>

		<form id = "forgotform" name = "forgotform" method="post" action="manageuser.php?action=getnewpass" {literal} onsubmit="return validateCompleteForm(this,'input_error');"{/literal}>
			<fieldset>

			<div class="row">
				<label for="username" class="username">{#email#}:</label>
				<input type="text" class="text" name="email" id="email" required = "1" regexp="EMAIL" realname = "{#email#}" />
			</div>

			<div class="row">
				<label class="right">
					<input type="submit" value="Get new password" class="loginbutn big" />
				</label>
			</div>
			</fieldset>
		</form>
	</div>
	
	{if $forgoterror == 1}
	<div class="login-alert">
		User does not exist with such E-Mail.
	</div>
	{/if}
</div>
</body>
</html>

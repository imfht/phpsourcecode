{include file="header.tpl" title="Registration" showheader="no" jsload = "ajax"}


<div class="login">
	<div class="login-in">
		<div class="logo-name">
			<h1><a href = "http://2-plan.com/" title = "{$settings.name} Open Source project management"><img src="./templates/standard/images/logo-a.png" alt="{$settings.name}"  /></a></h1>
			<h2>{$settings.subtitle}</h2>
		</div>

		<form id = "loginform" name = "loginform" method="post" action="{$smarty.server.PHP_SELF}" onsubmit="return validateCompleteForm(this,'input_error');">
		<input type="hidden" name="action" value="register" />
			<fieldset>

			<div class="row">
				<label for="email">{#email#}:</label>
				<input type="text" class="text" name="email" id="email" required="1" regexp="EMAIL" realname="{#email#}" value="{$smarty.post.email}" />
			</div>

			<div class="row">
				<label for="pass">{#password#}:</label>
				<input type="password" class="text" name="pass" id="pass" required="1" realname="{#password#}" value="{$smarty.post.pass}" />
			</div>

			<div class="row">
				<label for="pass2">{#repeatpass#}:</label>
				<input type="password" class="text" name="pass2" id="pass2" required="1" realname="{#repeatpass#}" value="{$smarty.post.pass2}" />
			</div>

			<div class="row">
				<label for="captcha" style="padding-bottom:120px;">Image Text:</label>
				<div style="padding-top:12px;">
				Enter the text you see in the below image:<br />
				<div style="float:left">{php} echo captcha::form(); {/php}</div>
				</div>
			</div>

			<div class="row">
				<label class="right">
					<input type="checkbox" name="agree" value="1" /> I agree to the <a href="">Terms of Service</a> and <a href="">Privacy Policy</a>
				</label>
			</div>

			<div class="row">
				<label class="right">
					<input type="submit" value="Sign Up Now" class="loginbutn" />
				</label>
			</div>
			
			</fieldset>
		</form>
	</div>


	{if count($errors)>0}<div class="login-alert">{"<br />"|join:$errors}</div>{/if}
	{if $message!=""}<div class="login-message">{$message}</div>{/if}
</div>



</body>
</html>

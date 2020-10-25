{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
<script type="text/javascript">
	var there_are = '{l s='There are'}';
	var there_is = '{l s='There is'}';
	var label_errors = '{l s='errors'}';
	var label_error = '{l s='error'}';
</script>

<div id="container">	
	<div id="error" {if !isset($errors)}class="hide"{/if}>
		{if isset($errors)}
			<h3>{if $nbErrors > 1}{l s='There are %d errors.' sprintf=$nbErrors}{else}{l s='There is %d error.' sprintf=$nbErrors}{/if}</h3>
			<ol style="margin: 0 0 0 20px;">
			{foreach from=$errors item="error"}
				<li>{$error}</li>
			{/foreach}
			</ol>
		{/if}
	</div>
	<br />
	{if isset($warningSslMessage)}
		<div class="warn">
			{$warningSslMessage}
		</div>
	{/if}
	<div id="login">
		<h1>{$shop_name}</h1>
	{if !isset($wrong_folder_name)}
		<form action="#" id="login_form" method="post">
			<div class="field">
				<label for="email">{l s='E-mail address:'}</label>
				<input type="text" id="email" name="email" class="input email_field" value="{if isset($email)}{$email|escape:'htmlall':'UTF-8'}{/if}" />
			</div>

			<div class="field">
				<label for="passwd">{l s='Password:'}</label>
				<input id="passwd" type="password" name="passwd" class="input password_field" value="{if isset($password)}{$password}{/if}"/>
			</div>

			<div class="field">
				<input type="submit" name="submitLogin" value="{l s='Log in'}" class="button fl margin-right-5" />

				<p class="fl no-margin hide ajax-loader">
					<img src="../img/loader.gif" alt="" />
				</p>

				<p class="fr no-margin">
					<a href="#" class="show-forgot-password">{l s='Lost password?'}</a>
				</p>
				<div class="clear"></div>
			</div>
			<input type="hidden" name="redirect" id="redirect" value="{$redirect}"/>
		</form>

		<form action="#" id="forgot_password_form" method="post" class="hide">
			<h2 class="no-margin">{l s='Forgot your password?'}</h2>
			<p class="bold">{l s='Please enter the e-mail address you provided during the registration process in order to receive your access code by e-mail'}</p>

			<div class="field">
				<label>{l s='E-mail address:'}</label>
				<input type="text" name="email_forgot" id="email_forgot" class="input email_field" />
			</div>

			<div class="field">
				<input type="submit" name="submit" value="{l s='Send'}" class="button fl margin-right-5" />

				<p class="fl no-margin hide ajax-loader">
					<img src="../img/loader.gif" alt=""  />
				</p>

				<p class="fr no-margin">
					<a href="#" class="show-login-form">{l s='Back to login'}</a>
				</p>
				<div class="clear"></div>
			</div>
		</form>
	{else}
		<div class="padding-30">
			<p>{l s='For security reasons, you cannot connect to the Back Office until after you have:'}</p>
			<ul>
				<li>{l s='deleted the /install folder'}</li>
				<li>{l s='renamed the /admin folder (e.g. /admin123%d)' sprintf=$randomNb}</li>
			</ul>
			<p>{l s='Please then access this page by the new URL (e.g. http://www.yoursite.com/admin123%d)' sprintf=$randomNb}</p>
		</div>
	{/if}
	</div>
	<h2><a href="http://www.milebiz.com">&copy; 2011 - {$smarty.now|date_format:"%Y"} Copyright by MileBiz. all rights reserved.</a></h2>
</div>

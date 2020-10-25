{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{capture assign='page_title'}{l s='Forgot your password?'}{/capture}
{include file='./page-title.tpl'}

{include file="$tpl_dir./errors.tpl"}
<div data-role="content" id="content">
	{if isset($confirmation) && $confirmation == 1}
	<p class="success">{l s='Your password has been successfully reset and a confirmation has been sent to your e-mail address:'} {$email|escape:'htmlall':'UTF-8'}</p>
	{elseif isset($confirmation) && $confirmation == 2}
	<p class="success">{l s='A confirmation e-mail has been sent to your address:'} {$email|escape:'htmlall':'UTF-8'}</p>
	{else}
	<p>{l s='Please enter the e-mail address used to register. We will send your new password to that address.'}</p>
	<form action="{$request_uri|escape:'htmlall':'UTF-8'}" method="post" class="std" id="form_forgotpassword">
		<fieldset>
			<label for="email">{l s='E-mail:'}</label>
			<input type="text" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{/if}" />
			<input type="submit" class="button" data-theme="a" value="{l s='Retrieve Password'}" />
		</fieldset>
	</form>
	{/if}
	<p class="clear">
		<a href="{$link->getPageLink('authentication', true)}" title="{l s='Return to Login'}"><img src="{$img_dir}icon/my-account.gif" alt="{l s='Return to Login'}" class="icon" /></a><a href="{$link->getPageLink('authentication')}" title="{l s='Back to Login'}" data-ajax="false">{l s='Back to Login'}</a>
	</p>
</div><!-- /content -->

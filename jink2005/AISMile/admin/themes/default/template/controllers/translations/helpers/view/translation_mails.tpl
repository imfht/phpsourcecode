{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}

	{$tinyMCE}

	{if !empty($limit_warning)}
	<div class="warn">
		{if $limit_warning['error_type'] == 'suhosin'}
			{l s='Warning, your hosting provider is using the suhosin patch for PHP, which limits the maximum number of fields to post in a form:'}

			<b>{$limit_warning['post.max_vars']}</b> {l s='for suhosin.post.max_vars.'}<br/>
			<b>{$limit_warning['request.max_vars']}</b> {l s='for suhosin.request.max_vars.'}<br/>
			{l s='Please ask your hosting provider to increase the suhosin post and request a limit of'}
		{else}
			{l s='Warning, your PHP configuration limits the maximum number of fields to post in a form:'}<br/>
			<b>{$limit_warning['max_input_vars']}</b> {l s='for max_input_vars.'}<br/>
			{l s='Please ask your hosting provider to increase the this limit to'}
		{/if}
		{l s='%s at least or edit the translation file manually.' sprintf=$limit_warning['needed_limit']}
	</div>
	{else}

		<div class="hint" style="display:block;">
			<ul style="margin-left:30px;list-style-type:disc;">
				<li>{l s='Click on the titles to open fieldsets'}.</li>
				<li>{l s='Some sentences to translate use this syntax: %s... These are variables, and MileBiz take care of replacing them before displaying your translation. You must leave these in your translations, and place them appropriately in your sentence.' sprintf='%d, %s, %1$s, %2$d'}</li>
			</ul>
		</div><br /><br />

		<form method="post" id="{$table}_form" action="{$url_submit}" class="form">
			{$toggle_button}
			<input type="hidden" name="lang" value="{$lang}" />
			<input type="hidden" name="type" value="{$type}" />
			<input type="hidden" name="theme" value="{$theme}" />
			<input type="submit" id="{$table}_form_submit_btn" name="submitTranslations{$type|ucfirst}" value="{l s='Update translations'}" class="button" />

			<script type="text/javascript">
				$(document).ready(function(){
					$('a.useSpecialSyntax').click(function(){
						var syntax = $(this).find('img').attr('alt');
						$('#BoxUseSpecialSyntax .syntax span').html(syntax+".");
						$('#BoxUseSpecialSyntax').toggle(1000);
					});
					$('#BoxUseSpecialSyntax').click(function(){
						$('#BoxUseSpecialSyntax').toggle(1000);
					});
				});
			</script>

			<div id="BoxUseSpecialSyntax">
				<div class="warn">
					<p class="syntax">
						{l s='This expression uses this special syntax:'} <span>%d.</span><br />
						{l s='You must use this syntax in your translations. Here are several examples:'}
					</p>
					<ul>
						<li><em>There are <strong>%d</strong> products</em> ("<strong>%d</strong>" {l s='will be replaced by a number'}).</li>
						<li><em>List of pages in <strong>%s</strong>:</em> ("<strong>%s</strong>" {l s='will be replaced by a string'}).</li>
						<li><em>Feature: <strong>%1$s</strong> (<strong>%2$d</strong> values)</em> ("<strong>n$</strong>" {l s='is used for the order of the arguments'}).</li>
					</ul>
				</div>
			</div>

			<h2>{l s='Core e-mails:'}</h2>
			<p class="preference_description">{l s='List of emails which are in the folder'} <strong>"mails/{$lang|strtolower}/"</strong></p>
			{$mail_content}

			<h2>{l s='Module e-mails:'}</h2>
			<p class="preference_description">{l s='List of emails which are in the folder'} <strong>"modules/name_of_module/mails/{$lang|strtolower}/"</strong></p>
			{foreach $module_mails as $module_name => $mails}
				{$mails['display']}
			{/foreach}
		</form>
	{/if}

{/block}

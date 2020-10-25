{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{if isset($errors) && $errors}
	<script type="text/javascript">
		{literal}
		function popErrorMessage(errorTitle, errorMessage)
		{
			$('<div class="error-box"><h1>'+errorTitle+'</h1>'+errorMessage+'</div>').appendTo('body');
			var close_bt = '';
			close_bt += '<a href="#" data-role="button" data-icon="delete" data-iconpos="notext" data-theme="e" class="close-bt" >delete</a>';
			$('.error-box').append(close_bt);
			$('.error-box').find('.close-bt').button();
			$('.error-box').find('.close-bt').bind('click', function(e)
			{
				e.preventDefault();
				$('.error-box').fadeOut(400, function() {
					$(this).remove();
				})
			});
		}
		$(function()
		{
			var errorTitle = '{/literal}{if $errors|@count > 1}{l s='There are %d errors:' sprintf=$errors|@count}{else}{l s='There is %d error:' sprintf=$errors|@count}{/if}{literal}';
			var errorMessage = '<ol>';
			{/literal}
			{foreach from=$errors key=k item=error}
			errorMessage += '<li>{$error|addslashes}</li>';
			{/foreach}
			{literal}
			errorMessage += '</ol>';
			popErrorMessage(errorTitle, errorMessage);
		});
		{/literal}
	</script>
{/if}

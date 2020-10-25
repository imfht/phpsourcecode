{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<script type="text/javascript">
	var textMsg = "{l s='This is a test message, your server is now configured to send e-mail'}";
	var textSubject = "{l s='Test message - MileBiz'}";
	var textSendOk = "{l s='A test e-mail has been sent to the e-mail address you specified'}";
	var textSendError= "{l s='Error: please check your configuration'}";
	var token_mail = '{$token}';
	var errorMail = "{l s='This e-mail address is invalid'}";
	$(document).ready(function() {
		if ($('input[name=PS_MAIL_METHOD]:checked').val() == 2)
			$('#smtp').show();
	});
</script>
<script type="text/javascript" src="../js/sendMailTest.js"></script>

{$content}



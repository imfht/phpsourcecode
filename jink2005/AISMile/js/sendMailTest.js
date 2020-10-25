/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

//constant
verifMailREGEX = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/;

function verifyMail(testMsg, testSubject)
{
	$("#mailResultCheck").removeClass("ok").removeClass('fail').html('<img src="../img/admin/ajax-loader.gif" alt="" />');
	$("#mailResultCheck").slideDown("slow");

	//local verifications
	if ($("#testEmail[value=]").length > 0)
	{
		$("#mailResultCheck").addClass("fail").removeClass("ok").removeClass('userInfos').html(errorMail);
		return false;
	}
	else if (!verifMailREGEX.test( $("#testEmail").val() ))
	{
		$("#mailResultCheck").addClass("fail").removeClass("ok").removeClass('userInfos').html(errorMail);
		return false;
	}
	else
	{
		//external verifications and sets
		$.ajax(
		{
		   url: "index.php",
		   cache: false,
		   type : "POST",
		   data:
			{
				"mailMethod"	: (($("input[name=PS_MAIL_METHOD]:checked").val() == 2) ? "smtp" : "native"),
				"smtpSrv"		: $("input[name=PS_MAIL_SERVER]").val(),
				"testEmail"		: $("#testEmail").val(),
				"smtpLogin"		: $("input[name=PS_MAIL_USER]").val(),
				"smtpPassword"	: $("input[name=PS_MAIL_PASSWD]").val(),
				"smtpPort"		: $("input[name=PS_MAIL_SMTP_PORT]").val(),
				"smtpEnc"		: $("select[name=PS_MAIL_SMTP_ENCRYPTION]").val(),
				"testMsg"		: textMsg,
				"testSubject"	: textSubject,
				"token"			: token_mail,
				"ajax"			: 1,
				"tab"				: 'AdminEmails',
				"action"			: 'sendMailTest'
			},
		   success: function(ret)
		   {
				if (ret == "ok")
				{
					$("#mailResultCheck").addClass("ok").removeClass("fail").removeClass('userInfos').html(textSendOk);
					mailIsOk = true;
				}
				else
				{
					mailIsOk = false;
					$("#mailResultCheck").addClass("fail").removeClass("ok").removeClass('userInfos').html(textSendError + '<br />' + ret);
				}
		   }
		 }
		 );
	}
}

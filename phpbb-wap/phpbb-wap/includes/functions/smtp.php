<?php
/**
* @package phpBB-WAP
* @copyright (c) phpBB Group
* @Оптимизация под WAP: Гутник Игорь ( чел ).
* @简体中文：中文phpBB-WAP团队
* @license http://opensource.org/licenses/gpl-license.php
**/

/**
* 这是一款自由软件, 您可以在 Free Software Foundation 发布的
* GNU General Public License 的条款下重新发布或修改; 您可以
* 选择目前 version 2 这个版本（亦可以选择任何更新的版本，由
* 你喜欢）作为新的牌照.
**/

define('SMTP_INCLUDED', true);

/*
* 对邮件服务器端返回代码进行验证
*/
function server_parse($socket, $response, $line = __LINE__) 
{
	$server_response = '';

	while (substr($server_response, 3, 1) != ' ') 
	{
		if (!($server_response = fgets($socket, 256))) 
		{ 
			trigger_error('无法获取邮件服务器的响应码', E_USER_WARNING);
		} 
	}
	if (substr($server_response, 0, 3) != $response) 
	{ 
		trigger_error("邮件发送问题. 响应码: $server_response", E_USER_WARNING);
	} 
}

/*
* 发送电子邮件
*/
function smtpmail($mail_to, $subject, $message, $headers = '')
{
	global $board_config;

	$message = preg_replace("#(?<!\r)\n#si", "\r\n", $message);

	if ($headers != '')
	{
		if (is_array($headers))
		{
			if (count($headers) > 1)
			{
				$headers = join("\n", $headers);
			}
			else
			{
				$headers = $headers[0];
			}
		}
		$headers = chop($headers);
		$headers = preg_replace('#(?<!\r)\n#si', "\r\n", $headers);
		$header_array = explode("\r\n", $headers);
		@reset($header_array);

		$headers = '';
		$cc = '';
		foreach($header_array as $header)
		{
			if (preg_match('#^cc:#si', $header))
			{
				$cc = preg_replace('#^cc:(.*)#si', '\1', $header);
			}
			else if (preg_match('#^bcc:#si', $header))
			{
				$bcc = preg_replace('#^bcc:(.*)#si', '\1', $header);
				$header = '';
			}
			$headers .= ($header != '') ? $header . "\r\n" : '';
		}

		$headers = chop($headers);
		$cc = explode(', ', $cc);
		$bcc = explode(', ', $bcc);
	}

	if (trim($subject) == '')
	{
		trigger_error('没有输入邮件的标题', E_USER_ERROR);
	}

	if (trim($message) == '')
	{
		trigger_error('没有输入邮件的内容', E_USER_ERROR);
	}

	// 打开 Socket 链接
	if( !$socket = @fsockopen($board_config['smtp_host'], 25, $errno, $errstr, 20) )
	{
		trigger_error('无法链接 SMTP 服务器：' . $errno . ' ：' . $errstr, E_USER_WARNING);
	}

	server_parse($socket, 220, __LINE__);

	// 如果smtp服务器需要验证帐号密码
	if( !empty($board_config['smtp_username']) && !empty($board_config['smtp_password']) )
	{ 

		// 向服务器说明身份
		fputs($socket, "EHLO " . $board_config['smtp_host'] . "\r\n");
		server_parse($socket, 250, __LINE__);

		// 向服务器请求认证
		fputs($socket, "AUTH LOGIN\r\n");
		server_parse($socket, 334, __LINE__);

		// 发送经过Base64转码后的用户名
		fputs($socket, base64_encode($board_config['smtp_username']) . "\r\n");
		server_parse($socket, 334, __LINE__);

		// 发送经过Base64转码后的密码
		fputs($socket, base64_encode($board_config['smtp_password']) . "\r\n");
		server_parse($socket, 235, __LINE__);
	}
	// 不需要验证权限，向服务器标识用户身份
	else
	{
		// 向服务器说明身份
		fputs($socket, "HELO " . $board_config['smtp_host'] . "\r\n");
		server_parse($socket, 250, __LINE__);
	}

	// 在主机上初始化一个邮件会话
	fputs($socket, "MAIL FROM: <" . $board_config['board_email'] . ">\r\n");
	server_parse($socket, 250, __LINE__);

	$to_header = '';
  
	// 告诉服务器邮件发送到哪里
	$mail_to = (trim($mail_to) == '') ? 'Undisclosed-recipients:;' : trim($mail_to);

	if (preg_match('#[^ ]+\@[^ ]+#', $mail_to))
	{
		// 您不能给自己发送邮件，这会遭到邮件服务商的拒绝，例如腾讯的QQ邮箱
		if ($mail_to == $board_config['board_email'])
		{
			trigger_error('您不能给自己发送邮件', E_USER_ERROR);
		}

		fputs($socket, "RCPT TO: <$mail_to>\r\n");
		server_parse($socket, 250, __LINE__);
	}

	@reset($bcc);
	foreach($bcc as $bcc_address)
	{
		$bcc_address = trim($bcc_address);
		if (preg_match('#[^ ]+\@[^ ]+#', $bcc_address))
		{
			fputs($socket, "RCPT TO: <$bcc_address>\r\n");
			server_parse($socket, 250, __LINE__);
		}
	}

	@reset($cc);
	foreach($cc as $cc_address)
	{
		$cc_address = trim($cc_address);
		if (preg_match('#[^ ]+\@[^ ]+#', $cc_address))
		{
			fputs($socket, "RCPT TO: <$cc_address>\r\n");
			server_parse($socket, 250, __LINE__);
		}
	}

	// 告诉服务器自己准备发送邮件正文
	fputs($socket, "DATA\r\n");
	// 服务器返回354，表示自己已经作好接受邮件的准备
	server_parse($socket, 354, __LINE__);
	// 发送邮件正文
	fputs($socket, "Subject: $subject\r\n");
	fputs($socket, "To: $mail_to\r\n");
	fputs($socket, "$headers\r\n\r\n");
	fputs($socket, "$message\r\n");
	fputs($socket, ".\r\n");// 标记邮件的结束，发送邮件
	server_parse($socket, 250, __LINE__);
	fputs($socket, "QUIT\r\n");// 终止邮件会话
	//server_parse($socket, "221", __LINE__);
	fclose($socket);

	return true;
}

?>
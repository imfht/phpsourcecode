<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

class Email extends PHPMailer
{
	
	/*
	*	实例 Email(发件人, 收件人, 邮件标题, 内容, 附件);
	*/
	function __construct($from, $to, $subject, $message, $attach = array()) {

		if (!$GLOBALS['C']['send_mail']) {

			Message(INFO, L('提示'), L('开启Email发送'));
		
		}

		// 使用SMTP发送邮件
		if ($GLOBALS['C']['smtp']) {

			$this->isSMTP();

			if ($GLOBALS['C']['smtp_secure']) {
				$this->SMTPSecure = "ssl";
			}

			$this->Host = $GLOBALS['C']['smtp_host'];

			$this->Port = $GLOBALS['C']['smtp_port'];

			$this->SMTPAuth = true;

			$this->Username = $GLOBALS['C']['smtp_username'];

			$this->Password = $GLOBALS['C']['smtp_password'];
		}

		$this->setFrom($from);

		foreach ($to as $name => $Address) {

			$this->addAddress($Address, $name);		
		}
		

		$this->Subject = $subject;

		$this->msgHTML($message);

		if (!empty($attach)) {
			foreach ($attach as $attach_file) {
				$this->addAttachment($attach_file);
			}
		}
	}
}

?>
<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

class InstallModelMail extends InstallAbstractModel
{
	/**
	 * @param bool $smtp_checked
	 * @param string $server
	 * @param string $login
	 * @param string $password
	 * @param int $port
	 * @param string $encryption
	 * @param string $email
	 */
	public function __construct($smtp_checked, $server, $login, $password, $port, $encryption, $email)
	{
		parent::__construct();

		require_once(_PS_INSTALL_PATH_.'../tools/swift/Swift.php');
		require_once(_PS_INSTALL_PATH_.'../tools/swift/Swift/Connection/SMTP.php');
		require_once(_PS_INSTALL_PATH_.'../tools/swift/Swift/Connection/NativeMail.php');

		$this->smtp_checked = $smtp_checked;
		$this->server = $server;
		$this->login = $login;
		$this->password = $password;
		$this->port = $port;
		$this->encryption = $encryption;
		$this->email = $email;
	}

	/**
	 * Send a mail
	 *
	 * @param string $subject
	 * @param string $content
	 * @return bool|string false is everything was fine, or error string
	 */
	public function send($subject, $content)
	{
		try
		{
			// Test with custom SMTP connection
			if ($this->smtp_checked)
			{

				$smtp = new Swift_Connection_SMTP($this->server, $this->port, ($this->encryption == "off") ? Swift_Connection_SMTP::ENC_OFF : (($this->encryption == "tls") ? Swift_Connection_SMTP::ENC_TLS : Swift_Connection_SMTP::ENC_SSL));
				$smtp->setUsername($this->login);
				$smtp->setpassword($this->password);
				$smtp->setTimeout(5);
				$swift = new Swift($smtp);
			}
			else
				// Test with normal PHP mail() call
				$swift = new Swift(new Swift_Connection_NativeMail());

			$message = new Swift_Message($subject, $content, 'text/html');
			if (@$swift->send($message, $this->email, 'no-reply@'.Tools::getHttpHost(false, false, true)))
				$result = false;
			else
				$result = 'Could not send message';

			$swift->disconnect();
		}
		catch (Swift_Exception $e)
		{
			$result = $e->getMessage();
		}

		return $result;
	}

}

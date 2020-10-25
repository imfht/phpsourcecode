<?php namespace qeephp\mail;

use Swift_Mailer;
use Swift_SmtpTransport as SmtpTransport;
use Swift_MailTransport as MailTransport;
use Swift_SendmailTransport as SendmailTransport;

class Provider
{
	
	/**
	 * Register the Swift Mailer instance.
	 *
	 * @return \Swift_Mailer
	 */
	public static function registerSwiftMailer($config)
	{
		$self = new static();
		return new Swift_Mailer( $self->registerSwiftTransport($config) );
	}

	/**
	 * Register the Swift Transport instance.
	 *
	 * @param  array  $config
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function registerSwiftTransport($config)
	{
		switch ($config['driver'])
		{
			case 'smtp':
				return $this->registerSmtpTransport($config);

			case 'sendmail':
				return $this->registerSendmailTransport($config);

			case 'mail':
				return $this->registerMailTransport($config);

			default:
				throw new \InvalidArgumentException('Invalid mail driver.');
		}
	}

	/**
	 * Register the SMTP Swift Transport instance.
	 *
	 * @param  array  $config
	 * @return void
	 */
	protected function registerSmtpTransport($config)
	{
		// The Swift SMTP transport instance will allow us to use any SMTP backend
		// for delivering mail such as Sendgrid, Amazon SMS, or a custom server
		// a developer has available. We will just pass this configured host.
		$transport = SmtpTransport::newInstance($config['host'], $config['port']);

		if (!empty($config['encryption']))
		{
			$transport->setEncryption($config['encryption']);
		}

		// Once we have the transport we will check for the presence of a username
		// and password. If we have it we will set the credentials on the Swift
		// transporter instance so that we'll properly authenticate delivery.
		if (!empty($config['username']))
		{
			$transport->setUsername($config['username']);
			$transport->setPassword($config['password']);
		}

		return $transport;
	}

	/**
	 * Register the Sendmail Swift Transport instance.
	 *
	 * @param  array  $config
	 * @return void
	 */
	protected function registerSendmailTransport($config)
	{
		return SendmailTransport::newInstance($config['sendmail']);
	}

	/**
	 * Register the Mail Swift Transport instance.
	 *
	 * @param  array  $config
	 * @return void
	 */
	protected function registerMailTransport($config)
	{
		return MailTransport::newInstance();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('mailer', 'swift.mailer', 'swift.transport');
	}

}

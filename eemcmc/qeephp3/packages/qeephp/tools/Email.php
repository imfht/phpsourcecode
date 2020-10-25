<?php namespace qeephp\tools;

use qeephp\mvc\App;
use qeephp\mail\Mailer;

/**
 * 邮件工具
 *
<code>
# mail 工具配置
		'mail' => array(
    'class'      => 'qeephp\\tools\\Email',

    'driver'       => 'smtp', #Supported: "smtp", "mail", "sendmail"
    'host' => 'smtp.exmail.qq.com',
    'port' => 465,
    'encryption' => 'ssl',
    'username' => "yourname",
    'password' =>"yourpass",
    'sendmail' => '/usr/sbin/sendmail -bs',#When using the "sendmail" driver to send e-mails
    'pretend' => 1,#启用此选项,邮件不会真正发送,而是写到日志文件中
    'logger'     => 'test', #使用日志对象

    'from' => array('address' => 'youraddress', 'name' => 'yoursitetitle'),
),
</code>
 */
class Email
{
	/**
     * 当前请求
     *
     * @var Mailer
     */
    public $mailer;
	
	/**
	 * Create a new Mailer instance.
	 *
	 * @param App $app
	 * @param array $config
	 */
	public function __construct(App $app, array $config) {
		$this->mailer = new Mailer($config);
	}

}

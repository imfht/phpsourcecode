<?php

namespace extend\mail;
/**
 * Mail 邮件发送类
 *
 * @author NewFuture
 *
 * @todo 完善和可配置
 */
class Mail
{
    protected $_config;
    private $_smtp;
    private $_view;
    private static $_instance = null;

    private function __construct($config = null)
    {
        $this->_config = $server = $config ? $config : \Config::get('mail');
        $this->_smtp = new Smtp();
        $this->_smtp->setServer($server['smtp'], $server['port'], $server['secure']);
    }

    /**
     * 发送验证邮件
     *
     * @param string $email [邮箱]
     * @param string $name [姓名]
     * @param string $link [验证链接]
     *
     * @return bool 发送结果
     */
    public static function sendVerifyCode($email, $number)
    {
        $instance = self::getInstance();
        $from = $instance->_config;
        $to = array('email' => $email, 'name' => $email);

        $msg['title'] = '验证邮件';
        $msg['body'] = $instance->getView()
            ->assign('email', $email)
            ->assign('info',$from)
            ->assign('number', $number)
            ->render('verify_code.tpl');
        return $instance->send($from, $to, $msg);
    }

    /**
     * 发送邮件
     *
     * @param string $to [接收方邮箱]
     * @param array $msg [发送信息]
     * @param mixed $from
     *
     * @return bool [发送结果]
     */
    public function send($from, $to, $msg)
    {
        $Message = new Message();
        $Message->setFrom($from['name'], $from['email'])
            ->addTo($to['name'], $to['email'])
            ->setSubject($msg['title'])
            ->setBody($msg['body']);
        return $this->_smtp
            ->setAuth($from['email'], $from['pwd'])
            ->send($Message);
    }

    /**
     * 获取邮件服务对象
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 获取模板引擎
     */
    private function getView()
    {
        if (!$this->_view) {
            $this->_view = new \Yaf_View_Simple(APP_PATH . '/library/extend/email/');
        }
        return $this->_view;
    }
}

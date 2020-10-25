<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\util;

use tfc\ap\ErrorException;

/**
 * Smtp class file
 * 简单邮件传输协议发送到邮件服务器（邮件传输代理MTA）
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Smtp.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.util
 * @since 1.0
 */
class Smtp
{
    /**
     * @var string SMTP主机地址
     */
    protected $_host = '';

    /**
     * @var integer SMTP端口号，默认是 25
     */
    protected $_port = 25;

    /**
     * @var string SMTP用户名
     */
    protected $_username = '';

    /**
     * @var string SMTP密码
     */
    protected $_password = '';

    /**
     * @var string 发件人地址，应该和用户名相同，如果不相同，有可能出错
     */
    protected $_fromMail = '';

    /**
     * @var boolean 是否验证SMTP用户名和SMTP密码
     */
    protected $_auth = true;

    /**
     * @var resource fsockopen句柄
     */
    protected $_stream = null;

    /**
     * @var boolean 是否显示发件人地址
     */
    protected $_showFromMail = true;

    /**
     * @var boolean 同时向多人发送邮件时，每个邮件中都会显示所有收件人，如果不想显示这些收件人地址，可以设为false
     */
    protected $_showToMail = true;

    /**
     * 构造方法：初始化SMTP主机地址、SMTP用户名、SMTP密码、是否显示发件人地址、是否显示收件人地址、发件人地址
     * <ul>
     * <li>{@link $host = 'smtp.qq.com'}</li>
     * <li>{@link $username = '******@qq.com'}</li>
     * <li>{@link $password = '******'}</li>
     * </ul>
     * @param string $host
     * @param string $username
     * @param string $password
     * @param boolean $showFromMail
     * @param boolean $showToMail
     * @param string $fromMail
     */
    public function __construct($host, $username, $password, $showFromMail = true, $showToMail = true, $fromMail = '')
    {
        $this->_host = $host;
        $this->_username = $username;
        $this->_password = $password;
        $this->_showFromMail = (boolean) $showFromMail;
        $this->_showToMail = (boolean) $showToMail;
        $this->_fromMail = $fromMail != '' ? $fromMail : $this->_username;
    }

    /**
     * 发送邮件
     * @param string $toMail
     * @param string $subject
     * @param string $body
     * @return boolean
     */
    public function sendMail($toMail, $subject, $body)
    {
        $this->open($this->_host, $this->_port);
        $this->authLogin($this->_username, $this->_password, $this->_auth);
        $this->setFromMail($this->_fromMail);

        if (strpos($toMail, ',') === false) {
            $this->setToMail($toMail);
        }
        else {
            foreach (explode(',', $toMail) as $value) {
                if (($value = trim($value)) !== '') {
                    $this->setToMail($value);
                }
            }
        }

        if (!$this->_showToMail) {
            $toMail = '';
        }

        $fromMail = $this->_showFromMail ? $this->_showFromMail : '';
        $this->sendData($subject, $body, $toMail, $fromMail);
        fwrite($this->_stream, "QUIT\r\n");
        $this->close();

        return true;
    }

    /**
     * 发送邮件正文数据
     * @param string $subject
     * @param string $body
     * @param string $toMail
     * @param string $fromMail
     * @return \tfc\util\Smtp
     * @throws ErrorException 如果反馈消息不是以354开头，抛出异常
     */
    public function sendData($subject, $body, $toMail = '', $fromMail = '')
    {
        fwrite($this->_stream, "DATA\r\n");
        $message = fgets($this->_stream, 512);
        if (($start = substr($message, 0, 3)) !== '354') {
            throw new ErrorException(sprintf(
                'Smtp send Data failed, message not start with "354", start "%s", subject "%s", message "%s".', $start, $subject, $message
            ));
        }

        $data = "To: $toMail\r\nFrom: $fromMail\r\nSubject: $subject\r\n\r\n$body\r\n.\r\n";
        fwrite($this->_stream, $data);
        return $this;
    }

    /**
     * 设置收件人地址
     * @param string $toMail
     * @return \tfc\util\Smtp
     * @throws ErrorException 如果反馈消息不是以250开头，抛出异常
     */
    public function setToMail($toMail)
    {
        if (($toMail = trim($toMail)) == '') {
            throw new ErrorException(
                'Smtp is unable to determine the to MAIL.'
            );
        }

        fwrite($this->_stream, "RCPT TO: <$toMail>\r\n");
        $message = fgets($this->_stream, 512);
        if (($start = substr($message, 0, 3)) === '250') {
            return $this;
        }

        fwrite($this->_stream, "RCPT TO: <$toMail>\r\n");
        $message = fgets($this->_stream, 512);
        if (($start = substr($message, 0, 3)) === '250') {
            return $this;
        }

        throw new ErrorException(sprintf(
            'Smtp set to MAIL failed, message not start with "250", start "%s", tomail "%s", message "%s".', $start, $toMail, $message
        ));
    }

    /**
     * 设置发件人地址
     * @param string $fromMail
     * @return \tfc\util\Smtp
     * @throws ErrorException 如果反馈消息不是以250开头，抛出异常
     */
    public function setFromMail($fromMail)
    {
        if (($fromMail = trim($fromMail)) == '') {
            throw new ErrorException(
                'Smtp is unable to determine the from MAIL.'
            );
        }

        fwrite($this->_stream, 'MAIL FROM: <' . preg_replace('/.*\<(.+?)\>.*/', '\\1', $fromMail) . ">\r\n");
        $message = fgets($this->_stream, 512);
        if (($start = substr($message, 0, 3)) === '250') {
            return $this;
        }

        fwrite($this->_stream, 'MAIL FROM: <' . preg_replace('/.*\<(.+?)\>.*/', '\\1', $fromMail) . ">\r\n");
        $message = fgets($this->_stream, 512);
        if (($start = substr($message, 0, 3)) === '250') {
            return $this;
        }

        throw new ErrorException(sprintf(
            'Smtp set from MAIL failed, message not start with "250", start "%s", frommail "%s", message "%s".', $start, $fromMail, $message
        ));
    }

    /**
     * 验证SMTP用户名和SMTP密码
     * @param string $username
     * @param string $password
     * @param boolean $auth
     * @return \tfc\util\Smtp
     * @throws ErrorException 如果发送用户名后反馈消息不是以220或250开头，抛出异常
     * @throws ErrorException 如果发送登录命令后反馈消息不是以334开头，抛出异常
     * @throws ErrorException 如果发送加密登录命令后反馈消息不是以334开头，抛出异常
     * @throws ErrorException 如果发送加密密码命令后反馈消息不是以235开头，抛出异常
     */
    public function authLogin($username, $password, $auth = true)
    {
        if (!$auth) {
            return $this;
        }

        fwrite($this->_stream, ($auth ? 'EHLO' : 'HELO') . " $username\r\n");
        $message = fgets($this->_stream, 512);
        $start = substr($message, 0, 3);
        if ($start !== '220' && $start !== '250') {
            throw new ErrorException(sprintf(
                'Smtp Auth login failed, message not start with "220" or "250", start "%s", username "%s", message "%s".', $start, $username, $message
            ));
        }

        while (true) {
            if (substr($message, 3, 1) !== '-' || empty($message)) {
                break;
            }

            $message = fgets($this->_stream, 512);
        }

        fwrite($this->_stream, "AUTH LOGIN\r\n");
        $message = fgets($this->_stream, 512);
        if (($start = substr($message, 0, 3)) !== '334') {
            throw new ErrorException(sprintf(
                'Smtp Auth login failed, message not start with "334", start "%s", username "%s", message "%s".', $start, $username, $message
            ));
        }

        fwrite($this->_stream, base64_encode($username) . "\r\n");
        $message = fgets($this->_stream, 512);
        if (($start = substr($message, 0, 3)) !== '334') {
            throw new ErrorException(sprintf(
                'Smtp Auth login failed, message not start with "334", start "%s", username "%s", message "%s".', $start, $username, $message
            ));
        }

        fwrite($this->_stream, base64_encode($password) . "\r\n");
        $message = fgets($this->_stream, 512);
        if (($start = substr($message, 0, 3)) != '235') {
            throw new ErrorException(sprintf(
                'Smtp Auth login failed, message not start with "235", start "%s", username "%s", password "", message "%s".', $start, $username, $password, $message
            ));
        }

        return $this;
    }

    /**
     * 连接SMTP服务器
     * @param string $host
     * @param integer $port
     * @return \tfc\util\Smtp
     * @throws ErrorException 如果连接SMTP服务器失败，抛出异常
     * @throws ErrorException 如果连接服务后反馈消息不是以220开头，抛出异常
     */
    public function open($host, $port = 25)
    {
        $errNo = 0;
        $errMsg = '';
        if (!$this->_stream = @fsockopen($host, $port, $errNo, $errMsg, 30)) {
            throw new ErrorException(sprintf(
                'Smtp unable to connect to the SMTP server, errNo "%d", errMsg "%s", host "%s", port "%d".', $errNo, $errMsg, $host, $port
            ));
        }

        stream_set_blocking($this->_stream, true);
        $message = fgets($this->_stream, 512);
        if (($start = substr($message, 0, 3)) !== '220') {
            throw new ErrorException(sprintf(
                'Smtp open failed, message not start with "220", start "%s", host "%s", port "%d".', $start, $host, $port
            ));
        }

        return $this;
    }

    /**
     * 关闭SMTP服务器
     * @return \tfc\util\Smtp
     */
    public function close()
    {
        if (is_resource($this->_stream)) {
            fclose($this->_stream);
        }

        $this->_stream = null;
        return $this;
    }

    /**
     * 获取SMTP主机地址
     * @return string
     */
    public function getHost()
    {
        return $this->_host;
    }

    /**
     * 获取SMTP端口号，默认是 25
     * @return integer
     */
    public function getPort()
    {
        return $this->_port;
    }

    /**
     * 获取SMTP用户名
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * 获取SMTP密码
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * 获取发件人地址，应该和用户名相同，如果不相同，有可能出错
     * @return string
     */
    public function getFromMail()
    {
        return $this->_fromMail;
    }
}

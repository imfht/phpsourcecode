<?php
/**
 * Trotri Foundation Classes
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright (c) 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tfc\ap;

/**
 * Response abstract class file
 * 响应模式发送基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Response.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
abstract class Response extends Application
{
    /**
     * @var integer HTTP响应状态码
     */
    protected $_statusCode = 200;

    /**
     * @var boolean Header中是否有页面重定向URL
     */
    protected $_isRedirect = false;

    /**
     * @var array 用于寄存所有的HTTP响应信息状态码和内容
     */
    protected static $_statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );

    /**
     * 获取HTTP响应状态码
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * 设置HTTP响应状态码
     * @param integer $statusCode
     * @return \tfc\ap\Response
     * @throws ErrorException 如果参数不是有效的状态码，抛出异常
     */
    public function setStatusCode($statusCode)
    {
        $statusCode = (int) $statusCode;
        if (isset(self::$_statusTexts[$statusCode])) {
            $isRedirect = (($statusCode >= 300) && ($statusCode <= 307)) ? true : false;
            $this->setIsRedirect($isRedirect);
            $this->_statusCode = $statusCode;
            return $this;
        }

        throw new ErrorException(sprintf(
            'Response HTTP response statusCode "%s" invalid.', $statusCode
        ));
    }

    /**
     * 获取HTTP响应信息内容
     * @return string
     */
    public function getStatusText()
    {
        return self::$_statusTexts[$this->_statusCode];
    }

    /**
     * 获取Header中是否有页面重定向URL
     * @return boolean
     */
    public function getIsRedirect()
    {
        return $this->_isRedirect;
    }

    /**
     * 设置Header中是否有页面重定向URL
     * @param boolean $isRedirect
     * @return \tfc\ap\Response
     */
    public function setIsRedirect($isRedirect = false)
    {
        $this->_isRedirect = (boolean) $isRedirect;
        return $this;
    }

    /**
     * 规范化Header名
     * @param string $name
     * @return string
     */
    public function normalizeHeader($name)
    {
        static $search = array('-', '_');
        $name = str_replace($search, ' ', $name);
        $name = str_replace(' ', '-', $name);
        return $name;
    }

    /**
     * 判断Header是否已经被发送
     * @param boolean $throwException
     * @return boolean
     * @throws RuntimeException 如果Header已经发送并且需要抛出异常，抛出异常
     */
    public function headersSent($throwException = false)
    {
        $headersSent = headers_sent($file, $line);
        if ($throwException && $headersSent) {
            throw new RuntimeException(sprintf(
                'Response headers already sent in "%s" on line "%d".', $file, $line
            ));
        }

        return $headersSent;
    }
}

<?php

/**
 * Response
 * @author 徐亚坤 hdyakun@sina.com
 */

namespace Madphp;

class Response extends Http\Response
{
    /**
     * 响应类实例
     * @var object
     */
    public static $instance = NULL;

    private function __construct() {}

    public static function init()
    {
        if (null === self::$responseInstance) {
            parent::init();
            // 默认禁止缓存
            self::setCache(0);
        }
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 获取状态码
     * @return int
     */
    public static function getStatus()
    {
        return self::$responseInstance->status;
    }

    /**
     * 设置状态码
     * @param int $code
     * @return $this
     */
    public static function setStatus($code = 200)
    {
        self::$responseInstance->status = (int) $code;
        return self::$instance;
    }

    /**
     * 设置文档类型
     * @param string $contentType
     * @return $this
     */
    public static function setContentType($contentType)
    {
        self::$responseInstance->contentType = $contentType;
        return self::$instance;
    }

    /**
     * 获取文档类型
     * @return string
     */
    public static function getContentType()
    {
        return self::$responseInstance->contentType;
    }

    /**
     * 设置头信息
     * @param string $k
     * @param mixed $v
     * @return $this
     */
    public static function setHeader($k, $v)
    {
        self::$responseInstance->headers[$k] = $v;
        return self::$instance;
    }

    /**
     * 获取头信息
     * @param string $key
     * @return mixed
     */
    public static function getHeader($key)
    {
        return self::$responseInstance->headers[$key];
    }

    /**
     * 获取所有头信息
     * @return array
     */
    public static function getHeaders()
    {
        return self::$responseInstance->headers;
    }

    /**
     * 获取状态码的描述
     * @param int $status
     * @return string
     */
    public static function getStatusText($status)
    {
        return 'HTTP/1.1 ' . $status . ' ' . ucwords(self::$statusTexts[$status]);
    }
    
    /**
     * 设置缓存
     * @param int $expire
     * @return $this
     */
    public static function setCache($expire = 0)
    {
        if ($expire <= 0) {
            self::$responseInstance->headers['Cache-Control'] = 'no-cache, no-store, max-age=0, must-revalidate';
            self::$responseInstance->headers['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
            self::$responseInstance->headers['Pragma'] = 'no-cache';
        } else {
            self::$responseInstance->headers['Last-Modified'] = gmdate('r', time());
            self::$responseInstance->headers['Expires'] = gmdate('r', time() + $expire);
            self::$responseInstance->headers['Cache-Control'] = 'max-age=' . $expire;
            unset(self::$responseInstance->headers['Pragma']);
        }
        return self::$instance;
    }

    /**
     * 设置内容文本
     * @param string $text
     * @return $this
     */
    public static function setBody($text)
    {
        self::$responseInstance->body = $text;
        return self::$instance;
    }

    /**
     * 获取内容文本
     * @return mixed
     */
    public static function getBody()
    {
        return self::$responseInstance->body;
    }

    /**
     * 判断是否已经发送
     * @return bool
     */
    public static function isSend()
    {
        return self::$responseInstance->isSendStatus;
    }

    /**
     * 发送返回请求
     * @return bool
     */
    public static function send()
    {
        // 判断有没有发送
        if (isset(self::$responseInstance->isSendStatus) && self::$responseInstance->isSendStatus) {
            return FALSE;
        }
        // 填入文档类型
        if (!(isset(self::$responseInstance->headers['Content-Type']) && self::$responseInstance->headers['Content-Type'])) {
            self::$responseInstance->headers['Content-Type'] = self::$responseInstance->contentType;
        }
        // 预设为无缓存
        if (!(isset(self::$responseInstance->headers['Cache-Control']) && self::$responseInstance->headers['Cache-Control'])) {
            self::setCache(0);
        }
        // 发送状态信息
        header('HTTP/1.1 ' . self::$responseInstance->status . ' ' . ucwords(self::$statusTexts[self::$responseInstance->status]));
        header('Status: ' . self::$responseInstance->status . ' ' . ucwords(self::$statusTexts[self::$responseInstance->status]));
        // 头信息
        foreach (self::$responseInstance->headers as $key => $value) {
            header($key . ': ' . $value);
        }
        // 输出内容
        if (self::$responseInstance->body) {
            echo self::$responseInstance->body;
        }
        self::$responseInstance->isSendStatus = TRUE;
        return TRUE;
    }

    /**
     * 跳转请求
     * @param string $url
     * @param int $code
     */
    public static function redirect($url, $code = 302)
    {
        self::$responseInstance->status = $code;
        self::setHeader('Location', $url);
        self::send();
    }

    /**
     * 错误请求
     * @param int $code
     * @param null|string $text
     */
    public static function error($code, $text = NULL)
    {
        self::$responseInstance->status = $code;
        if (!$text || !self::$responseInstance->body) {
            // 默认填充状态码描述
            $text = 'HTTP/1.1 ' . self::$responseInstance->status . ' ' . ucwords(self::$statusTexts[self::$responseInstance->status]);
        }
        self::$responseInstance->body = $text;
        self::send();
    }
}
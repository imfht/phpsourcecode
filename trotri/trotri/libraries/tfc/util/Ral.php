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
 * Ral class file
 * Resource Access Layer 需要开启CURL支持
 *
 * <pre>
 * CURLOPT_URL                读取Url
 * CURLOPT_HEADER             返回内容中不包含HTTP头
 * CURLOPT_FOLLOWLOCATION     将服务器返回的Location放在header中递归返回给服务器
 * CURLOPT_RETURNTRANSFER     将curl_exec()获取的信息以文件流的形式返回，而不直接输出
 * CURLOPT_NOSIGNAL           关闭alarm，支持ms级超时
 * CURLOPT_CONNECTTIMEOUT_MS  最长连接时间
 * CURLOPT_TIMEOUT_MS         最长读取和执行时间，精确到毫秒
 * CURLOPT_POST               发送POST请求，与表单提交效果是一样的
 * CURLOPT_POSTFIELDS         发送POST时，可以发的参数内容
 * </pre>
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Ral.php 1 2013-04-06 01:34:06Z huan.song $
 * @package tfc.util
 * @since 1.0
 */
class Ral
{
    /**
     * @var string 提交方式：GET
     */
    const METHOD_GET       = 'GET';

    /**
     * @var string 提交方式：POST
     */
    const METHOD_POST      = 'POST';

    /**
     * @var string 数据转码方式：字符串原样返回
     */
    const CONVERTER_STRING = 'STRING';

    /**
     * @var string 数据转码方式：将Json串转成数组后返回
     */
    const CONVERTER_JSON   = 'JSON';

    /**
     * @var string 数据转码方式：将序列串转成数组后返回
     */
    const CONVERTER_SERIAL = 'SERIAL';

    /**
     * @var string 服务器名称或IP地址
     */
    protected $_server;

    /**
     * @var integer 服务器端口号
     */
    protected $_port;

    /**
     * @var integer 连接超时，精确到：毫秒
     */
    protected $_connectTimeOutMs;

    /**
     * @var integer 执行超时，精确到：毫秒
     */
    protected $_timeOutMs;

    /**
     * @var string CURL获取数据后，将数据转码的方式
     */
    protected $_converter;

    /**
     * @var integer 日志ID，提交给服务器端，方便调试
     */
    protected $_logId;

    /**
     * 构造方法：初始化服务器名称或IP地址、服务器端口号、连接超时、执行超时、获取数据后转码方式
     * @param string $server
     * @param integer $port
     * @param integer $connectTimeOutMsMs
     * @param integer $timeOutMs
     * @param string $converter
     */
    public function __construct($server, $port = 80, $connectTimeOutMsMs = 30, $timeOutMs = 500, $converter = self::CONVERTER_STRING)
    {
        $this->setServer($server);
        $this->setPort($port);
        $this->setConnectTimeOutMs($connectTimeOutMsMs);
        $this->setTimeOutMs($timeOutMs);
        $this->setConverter($converter);
    }

    /**
     * CURL方式提交数据
     * @param string $pathinfo
     * @param array $params
     * @param string $method
     * @return mixed
     * @throws ErrorException 如果method不是POST或者GET，抛出异常
     * @throws ErrorException 如果curl执行失败，抛出异常
     */
    public function talk($pathinfo, array $params = array(), $method = 'GET')
    {
        $method = strtoupper($method);
        if (!defined('static::METHOD_' . $method)) {
            throw new ErrorException(sprintf(
                'Ral method "%s" is not a valid http method, must be "POST" or "GET" only', $method
            ));
        }
        $url = $this->getServer() . $pathinfo;
        $url = $this->applyLogId($url);

        $resource = curl_init();
        curl_setopt($resource, CURLOPT_URL,               $url);
        curl_setopt($resource, CURLOPT_HEADER,            0);
        curl_setopt($resource, CURLOPT_FOLLOWLOCATION,    true);
        curl_setopt($resource, CURLOPT_RETURNTRANSFER,    1);
        curl_setopt($resource, CURLOPT_NOSIGNAL,          1);
        curl_setopt($resource, CURLOPT_CONNECTTIMEOUT_MS, $this->getConnectTimeOut());
        curl_setopt($resource, CURLOPT_TIMEOUT_MS,        $this->getExecTimeOut());
        if ($this->isPost($method)) {
            curl_setopt($resource, CURLOPT_POST,          1);
            curl_setopt($resource, CURLOPT_POSTFIELDS,    $params);
        }
        $result = curl_exec($resource);
        if ($result === false) {
            $errNo = curl_errno($resource);
            $errMsg = curl_error($resource);
        }
        curl_close($resource);

        if ($result === false) {
            throw new ErrorException(sprintf(
                'Ral exec failed, pathinfo "%s", params "%s", method "%s", %s', $pathinfo, serialize($params), $method, $errMsg
            ), $errNo);
        }

        return $this->converter($result);
    }

    /**
     * CURL获取数据后，将数据转码
     * @param string $param
     * @return mixed
     * @throws ErrorException 如果转码方式无效，抛出异常
     */
    public function converter($param)
    {
        switch (true) {
            case $this->isJson(): return json_decode($param);
            case $this->isSerial(): return unserialize($param);
            case $this->isString(): return $param;
            default:
                throw new ErrorException(sprintf(
                    'Ral converter "%s" be invalid, must be "%s" or "%s" or "%s" only', $this->getConverter(), self::CONVERTER_JSON, self::CONVERTER_SERIAL, self::CONVERTER_STRING
                ));
        }
    }

    /**
     * 向URL上添加日志ID，提交给服务器
     * @param string $url
     * @return string
     * @throws ErrorException 如果日志ID小于或等于0，抛出异常
     */
    public function applyLogId($url)
    {
        if (($logId = $this->getLogId()) <= 0) {
            throw new ErrorException(sprintf(
                'Ral log id "%d" must be greater than zero', $logId
            ));
        }
        $url .= (strpos($url, '?') === false) ? '?' : '&';
        return $url . 'logid=' . $logId;
    }

    /**
     * 判断数据转码方式是否是字符串原样返回
     * @return boolean
     */
    public function isString()
    {
        return ($this->getConverter() === self::CONVERTER_STRING);
    }

    /**
     * 判断数据转码方式是否是将Json串转成数组后返回
     * @return boolean
     */
    public function isJson()
    {
        return ($this->getConverter() === self::CONVERTER_JSON);
    }

    /**
     * 判断数据转码方式是否是将序列串转成数组后返回
     * @return boolean
     */
    public function isSerial()
    {
        return ($this->getConverter() === self::CONVERTER_SERIAL);
    }

    /**
     * 判断提交方式是否是GET
     * @param string $method
     * @return boolean
     */
    public function isGet($method)
    {
        return ($method === self::METHOD_GET);
    }

    /**
     * 判断提交方式是否是POST
     * @param string $method
     * @return boolean
     */
    public function isPost($method)
    {
        return ($method === self::METHOD_POST);
    }

    /**
     * 获取日志ID
     * @return integer
     */
    public function getLogId()
    {
        return $this->_logId;
    }

    /**
     * 设置日志ID
     * @param integer $logId
     * @return \tfc\util\Ral
     */
    public function setLogId($logId)
    {
        $this->_logId = (int) $logId;
        return $this;
    }

    /**
     * 获取服务器名称或IP地址
     * @return string
     */
    public function getServer()
    {
        return $this->_server;
    }

    /**
     * 设置服务器名称或IP地址
     * @param string $server
     * @return \tfc\util\Ral
     */
    public function setServer($server)
    {
        if (substr($server, 0, 4) !== 'http') {
            $server = 'http://' . $server;
        }
        $this->_server = $server;
        return $this;
    }

    /**
     * 获取服务器端口号
     * @return integer
     */
    public function getPort()
    {
        return $this->_port;
    }

    /**
     * 设置服务器端口号
     * @param integer $port
     * @return \tfc\util\Ral
     */
    public function setPort($port)
    {
        $this->_port = (int) $port;
        return $this;
    }

    /**
     * 获取连接超时，精确到：毫秒
     * @return integer
     */
    public function getConnectTimeOutMs()
    {
        return $this->_connectTimeOutMs;
    }

    /**
     * 设置连接超时，精确到：毫秒
     * @param integer $connectTimeOutMs
     * @return \tfc\util\Ral
     */
    public function setConnectTimeOutMs($connectTimeOutMs)
    {
        $this->_connectTimeOutMs = (int) $connectTimeOutMs;
        return $this;
    }

    /**
     * 获取执行超时，精确到：毫秒
     * @return integer
     */
    public function getTimeOutMs()
    {
        return $this->_timeOutMs;
    }

    /**
     * 设置执行超时，精确到：毫秒
     * @param integer $timeOutMs
     * @return \tfc\util\Ral
     */
    public function setTimeOutMs($timeOutMs)
    {
        $this->_timeOutMs = (int) $timeOutMs;
        return $this;
    }

    /**
     * 获取数据转码的方式
     * @return string
     */
    public function getConverter()
    {
        return $this->_converter;
    }

    /**
     * 设置数据转码的方式
     * @param string $converter
     * @return \tfc\util\Ral
     */
    public function setConverter($converter)
    {
        $this->_converter = strtoupper($converter);
        return $this;
    }
}

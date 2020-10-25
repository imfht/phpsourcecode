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
 * Request abstract class file
 * 请求模式处理基类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Request.php 1 2013-03-29 16:48:06Z huan.song $
 * @package tfc.ap
 * @since 1.0
 */
abstract class Request extends Application
{
    const METHOD_OPTIONS  = 'OPTIONS';
    const METHOD_GET      = 'GET';
    const METHOD_HEAD     = 'HEAD';
    const METHOD_POST     = 'POST';
    const METHOD_PUT      = 'PUT';
    const METHOD_DELETE   = 'DELETE';
    const METHOD_TRACE    = 'TRACE';
    const METHOD_CONNECT  = 'CONNECT';
    const METHOD_PATCH    = 'PATCH';
    const METHOD_PROPFIND = 'PROPFIND';

    /**
     * @var string|null 表单提交方式，默认是GET
     */
    protected $_method = null;

    /**
     * @var array 用于寄存全局参数
     */
    protected $_params = array();

    /**
     * 获取所有全局参数
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * 批量设置全局参数
     * @param array $params
     * @return \tfc\ap\Request
     */
    public function setParams(array $params)
    {
        $this->_params += (array) $params;
        foreach ($this->_params as $key => $value) {
            if ($value === null) {
                unset($this->_params[$key]);
            }
        }

        return $this;
    }

    /**
     * 清除所有的全局参数
     * @return \tfc\ap\Request
     */
    public function clearParams()
    {
        $this->_params = array();
        return $this;
    }

    /**
     * 获取参数，并转化成整数，依次从全局参数、GET、POST、COOKIE中获取，如果都找不到，则返回默认值
     * @param string $key
     * @param integer $default
     * @return integer
     */
    public function getInteger($key, $default = 0)
    {
        return (int) $this->getParam($key, $default);
    }

    /**
     * 获取参数，并转化成字符串类型，依次从全局参数、GET、POST、COOKIE中获取，如果都找不到，则返回默认值
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getString($key, $default = '')
    {
        return (string) $this->getParam($key, $default);
    }

    /**
     * 获取参数，并转化成去了两边空白符的字符串类型，依次从全局参数、GET、POST、COOKIE中获取，如果都找不到，则返回默认值
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getTrim($key, $default = '')
    {
        return trim($this->getString($key, $default));
    }

    /**
     * 获取参数，依次从全局参数、GET、POST、COOKIE中获取，如果都找不到，则返回默认值
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        $key = (string) $key;
        switch (true) {
            case isset($this->_params[$key]):
                return $this->_params[$key];
            case isset($_GET[$key]):
                return $_GET[$key];
            case isset($_POST[$key]):
                return $_POST[$key];
            case isset($_COOKIE[$key]):
                return $_COOKIE[$key];
            default:
                return $default;
        }
    }

    /**
     * 设置一个全局参数
     * @param string $key
     * @param mixed $value
     * @return \tfc\ap\Request
     */
    public function setParam($key, $value)
    {
        $key = (string) $key;
        if ($value !== null) {
            $this->_params[$key] = $value;
            return $this;
        }

        if (isset($this->_params[$key])) {
            unset($this->_params[$key]);
        }

        return $this;
    }

    /**
     * 通过键名从GET中获取数据，如果键名为null，返回所有GET数据，如果键名在GET中不存在，返回默认值
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function getQuery($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }

        return (isset($_GET[$key]) ? $_GET[$key] : $default);
    }

    /**
     * 通过键名从POST中获取数据，如果键名为null，返回所有POST数据，如果键名在POST中不存在，返回默认值
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function getPost($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }

        return (isset($_POST[$key]) ? $_POST[$key] : $default);
    }

    /**
     * 通过键名从SERVER中获取数据，如果键名为null，返回所有SERVER数据，如果键名在SERVER中不存在，返回默认值
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function getServer($key = null, $default = null)
    {
        if ($key === null) {
            return $_SERVER;
        }

        return (isset($_SERVER[$key]) ? $_SERVER[$key] : $default);
    }

    /**
     * 通过键名从COOKIE中获取数据，如果键名为null，返回所有COOKIE数据，如果键名在COOKIE中不存在，返回默认值
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function getCookie($key = null, $default = null)
    {
        if ($key === null) {
            return $_COOKIE;
        }

        return (isset($_COOKIE[$key]) ? $_COOKIE[$key] : $default);
    }

    /**
     * 获取表单提交方式，默认从SERVER中获取，如果获取不到，默认为GET
     * @return string
     */
    public function getMethod()
    {
        if ($this->_method === null) {
            $this->setMethod();
        }

        return $this->_method;
    }

    /**
     * 设置表单提交方式，如果参数为null，默认从SERVER中获取，如果获取不到，默认为GET
     * @param string|null $method
     * @return \tfc\ap\Request
     * @throws InvalidArgumentException 如果参数不是有效的表单提交方式，抛出异常
     */
    public function setMethod($method = null)
    {
        if ($method === null) {
            $method = strtoupper($this->getServer('REQUEST_METHOD', ''));
            if ($method === '') {
                $method = self::METHOD_GET;
            }
        }
        else {
            $method = strtoupper((string) $method);
            if (!defined('static::METHOD_' . $method)) {
                throw new InvalidArgumentException(sprintf(
                    'Request HTTP method "%s" invalid.', $method
                ));
            }
        }

        $this->_method = $method;
        return $this;
    }

    /**
     * 判断提交方式是否是OPTIONS
     * @return boolean
     */
    public function isOptions()
    {
        return ($this->getMethod() === self::METHOD_OPTIONS);
    }

    /**
     * 判断提交方式是否是GET
     * @return boolean
     */
    public function isGet()
    {
        return ($this->getMethod() === self::METHOD_GET);
    }

    /**
     * 判断提交方式是否是HEAD
     * @return boolean
     */
    public function isHead()
    {
        return ($this->getMethod() === self::METHOD_HEAD);
    }

    /**
     * 判断提交方式是否是POST
     * @return boolean
     */
    public function isPost()
    {
        return ($this->getMethod() === self::METHOD_POST);
    }

    /**
     * 判断提交方式是否是PUT
     * @return boolean
     */
    public function isPut()
    {
        return ($this->getMethod() === self::METHOD_PUT);
    }

    /**
     * 判断提交方式是否是DELETE
     * @return boolean
     */
    public function isDelete()
    {
        return ($this->getMethod() === self::METHOD_DELETE);
    }

    /**
     * 判断提交方式是否是TRACE
     * @return boolean
     */
    public function isTrace()
    {
        return ($this->getMethod() === self::METHOD_TRACE);
    }

    /**
     * 判断提交方式是否是CONNECT
     * @return boolean
     */
    public function isConnect()
    {
        return ($this->getMethod() === self::METHOD_CONNECT);
    }

    /**
     * 判断提交方式是否是PATCH
     * @return boolean
     */
    public function isPatch()
    {
        return ($this->getMethod() === self::METHOD_PATCH);
    }

    /**
     * 判断提交方式是否是PROPFIND
     * @return boolean
     */
    public function isPropFind()
    {
        return ($this->getMethod() === self::METHOD_PROPFIND);
    }
}

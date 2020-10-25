<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Request;
use Tang\Util\CSRF;
use Tang\Util\Format;
use Tang\Web\Browser;
use Tang\Web\Cookie;
use Tang\Services\RouteService;
use Tang\Web\Ip\ClientIpService;
use Tang\Web\Session\SessionService;

/**
 * Class WebRequest
 * @package Tang\Request
 */
class WebRequest extends BaseRequest implements IRequest
{
	protected $isSsl = NULL;
	protected $method;
	protected $data = array();
    /**
     * @var \Tang\Util\CSRF
     */
    protected $CSRF;
	public function __construct(CSRF $CSRF)
	{
        if (get_magic_quotes_gpc())
        {
            Format::htmlSpecialchars($_REQUEST,true);
            Format::htmlSpecialchars($_POST,true);
            Format::htmlSpecialchars($_GET,true);
            Format::addslashes($_COOKIE);
        } else
        {
            Format::addslashes($_REQUEST, true);
            Format::addslashes($_POST, true);
            Format::addslashes($_GET, true);
            Format::addslashes($_COOKIE);
        }
		$this->data = array(
			'get' => &$_GET,
			'post'=>&$_POST,
			'put' => array()
		);
		$this->initMethod();
        $CSRF->setRequest($this);
        $this->CSRF = $CSRF;

	}

    /**
     * @return bool
     */
    public function isCli()
	{
		return false;
	}
	/**
	 * 判断是否ssl
	 * @return boolean
	 */
	public function isSsl()
	{
		$this->isSsl == null && $this->isSsl = $this->initSsl();
		return $this->isSsl;
	}

    /**
     * 获取get参数 没有返回$default
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name,$default=null)
	{
		return $this->getData('get', $name,$default);
	}

	/**
	 * 获取post参数 没有返回$default
	 * @param string $name
     * @param mixed $default
	 */
	public function post($name,$default=null)
	{
		return $this->getData('post',$name,$default);
	}

    /**
     * 获取put参数 没有返回$default
     * @param string $name
     * @param mixed $default
     */
	public function put($name,$default=null)
	{
		return $this->getData('put', $name,$default);
	}

	/**
	 * 	获取客户端使用的 HTTP 数据传输方法（如 GET、POST 或 HEAD）。
	 */
	public function getHttpMethod()
	{
		return $this->method;
	}

    /**
     * 获取客户端IP信息
     * @return \Tang\Web\Ip\IClientIp
     */
    public function clientIp()
	{
		return ClientIpService::getService();
	}

    /**
     * 获取Cookie
     * @return Cookie\ICookie
     */
    public function cookie()
    {
        return Cookie::getService();
    }

    /**
     * 获取Session
     * @return \Tang\Web\Session\ISession
     */
    public function session()
    {
        return SessionService::getService();
    }

    /**
     * 获取CSXF
     * @return CSRF
     */
    public function CSRF()
    {
        return $this->CSRF;
    }
    /**
     * 获取浏览器信息
     * @return Browser\IBrowser
     */
    public function browser()
	{
		return Browser::getService();
	}

    /**
     * 获取Referrer
     */
    public function getUrlReferrer()
	{
		require isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER']:'';
	}
	protected function initSsl()
	{
		if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS'])))
		{
			return true;
		}elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] ))
		{
			return true;
		}
		return false;
	}
	protected function initMethod()
	{
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		if($method == 'put' || $method=='delete')
		{
			parse_str(file_get_contents('php://input'), $this->data['put']);
		}
        $this->method = $method;
	}
	protected function getData($method,$name,$default)
	{
		return isset($this->data[$method][$name]) ? $this->data[$method][$name] : $default;
	}
}
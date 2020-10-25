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
namespace Tang\Web\Browser;
/**
 * 浏览器信息
 * Class HttpBrowserCapabilities
 * @package Tang\Web\Browser
 */
class HttpBrowserCapabilities implements IBrowser
{
	/**
	 * 浏览器名称
	 * @var string
	 */
	protected $browserName = 'unknow';
	/**
	 * 浏览器版本号
	 * @var string
	 */
	protected $browserVersion = '0.0.0';
	/**
	 * user agent
	 * @var string
	 */
	protected $userAgent;
	/**
	 * 是否支持ActiveX
	 * @var bool
	 */
	protected $supportsActiveX = false;
	/**
	 * 操作平台
	 * @var string
	 */
	protected $platform ;
	/**
	 * 浏览器语言
	 * @var string
	 */
	protected $language;
	public function __construct()
	{
		$this->init();
	}

	/**
	 * 获取浏览器名称
	 * @return string
	 */
	public function getBrowserName()
	{
		return $this->browserName;
	}

	/**
	 * 获取浏览器版本号
	 * @return string
	 */
	public function getBrowserVersion()
	{
		return $this->browserVersion;
	}

	/**
	 * 获取语言
	 * @return string
	 */
	public function getLanguage()
	{
		if(!$this->language)
		{
			if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			{
				preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
				$this->language = ucfirst(strtolower($matches[1]));
			}
			if(!$this->language || $this->language == 'Zh-hans-cn')
			{
				$this->language = 'Zh-cn';
			}
		}
		return $this->language;
	}

	/**
	 * 获取user agent
	 * @return string
	 */
	public function getUserAgent()
	{
		return $this->userAgent;
	}

    /**
     * 是否支持Activex
     * @return bool
     */
    public function getSupportsActiveX()
	{
		return $this->supportsActiveX;
	}

	/**
	 * 获取平台
	 * @return string
	 */
	public function getPlatform()
	{
		if($this->platform)
		{
			return $this->platform;
		}
		$platforms = array('win','max','linux','bsd','unix','sun','ibm','freebsd');
		foreach ($platforms as $platform)
		{
			if(preg_match('%('.$platform.'.+?);%i', $this->userAgent,$match))
			{
				$this->platform = $match[1];
				break;
			}
		}
		return $this->platform;
	}

	protected function init()
	{
		$browsers = array('firefox', 'msie', 'opera', 'chrome', 'safari','mozilla', 'seamonkey', 'konqueror', 'netscape','gecko','navigator', 'mosaic', 'lynx', 'amaya','omniweb', 'avant', 'camino', 'flock', 'aol');
		$this->userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
		foreach($browsers as $browser)
		{
			if (preg_match("#($browser)[/| ]?([0-9.]*)#", $this->userAgent, $match))
			{
				$this->browserName = $match[1] ;
				$this->browserVersion = $match[2] ;
				break ;
			}
		}
		if($this->browserName == 'msie' || $this->browserName == 'avant')
		{
			$this->supportsActiveX = true;
		}
	}
}
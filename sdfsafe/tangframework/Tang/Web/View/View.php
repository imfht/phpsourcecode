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
namespace Tang\Web\View;
use Tang\Services\ConfigService;
use Tang\Services\FileService;
use Tang\TangApplication;
use Tang\Web\Cookie;
use Tang\Web\Parameters;

class View implements IView
{
	protected static $shareData = array();
	protected $data = array();
	/**
	 * 主题 为了保持全局，采用静态变量
	 * @var string
	 */
	protected $theme = '';
	protected $config = array();
	public function setConfig(array $config)
	{
		$this->config = $config;
	}
	public function setTheme($theme)
	{
		$this->theme = $theme;
		Cookie::getService()->set($this->config['cookieThemeName'],$theme);
	}
	public function getTheme()
	{
		if(!$this->theme)
		{
			$theme = '';
			$getThemeName = $this->config['getThemeName'];
			if(isset($_GET[$getThemeName]) && $_GET[$getThemeName])
			{
				$theme = $_GET[$getThemeName];
			} else if($cookieThemeName = Cookie::getService()->get($this->config['cookieThemeName']))
			{
				$theme = $cookieThemeName;
			} else
			{
				$theme = $this->config['defaultTheme'];
			}
			$this->theme = $theme;
		}
		return $this->theme;
	}

	/**
	 * @return array
	 */
	public function getConfig()
	{
		return $this->config;
	}
	public function assginShare($key,$value)
	{
		static::$shareData[$key] = $value;
	}
	public function assgin($key,$value)
	{
		$this->data[$key] = $value;
	}
	public function getShare($key)
	{
		return isset(static::$shareData[$key])?static::$shareData[$key]:null;
	}
	public function get($key)
	{
		return isset($this->data[$key])?$this->data[$key]:null;
	}
	public function merginData(array $data)
	{
		$this->data = array_merge($this->data,$data);
	}
	public function display(Parameters $parameters,$viewType,$template='',$saveFilePath='',$isOutput=true)
	{
        $content = '';
		$instance = TemplateService::getService()->driver($viewType);
        $instance->setParameters($parameters);
        $instance->setView($this);
        $instance->setApplicationPath($this->config['applicationPath']);
		$instance->display(array_merge($this->data,static::$shareData),$content,$template);
        if(isset($_GET[$this->config['callback']]) && $_GET[$this->config['callback']])
        {
            $instance->callback($_GET[$this->config['callback']],$content);
        }
		if($saveFilePath)
		{
			FileService::write($saveFilePath,$content);
		}
		if(!$isOutput)
		{
			return $content;
		} else
		{
			echo $content;
		}
	}
	public function __clone()
	{
		$this->data = array();
	}
}
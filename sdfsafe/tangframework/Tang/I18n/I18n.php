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
namespace Tang\I18n;
use Tang\Exception\SystemException;

/**
 * 语言包
 * Class I18n
 * @package Tang\I18n
 */
class I18n implements II18n
{
	private $data = array();
	private $charset = 'Utf8';
	private $language = 'Zh-cn';
	private $loadModels = array();
	private $applicationDirectory = '';

    /**
     * @see II18n::get
     */
    public function get($key,array $args = array())
	{
		$index = strpos($key,'->');
		if($index > 0)
		{
			$this->loadModelLang(substr($key,0,$index));
			$key = substr($key,$index+2);
		}
		$lang = isset($this->data[$key]) && $this->data[$key] ? $this->data[$key]:$key;
		return vsprintf($lang,$args);
	}

    /**
     * @see II18n::setCharset
     */
	public function setCharset($charset)
	{
		$this->charset = $charset;
	}

    /**
     * @see II18n::setLanguage
     */
	public function setLanguage($language)
	{
		$this->language = $language;
	}

	/**
	 * @see II18n::setApplicationDirectory
	 */
	public function setApplicationDirectory($applicationDirectory)
	{
		$this->applicationDirectory = $applicationDirectory;
	}

    /**
     * @see II18n::loadModelLang
     */
	public function loadModelLang($modelName)
	{
		if(isset($this->loadModels[$modelName]))
		{
			return;
		} else 
		{
			$langPath = sprintf('%sLib%sI18n%s',$this->applicationDirectory,DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR.$this->getPath($modelName));
			$this->load($langPath);
			$this->loadModels[$modelName] = true;
		}
	}

    /**
     * @see II18n::loadFrameworkLanguage
     */
	public function loadFrameworkLanguage()
	{
		$langPath = __DIR__.DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.$this->getPath('language');
		$this->load($langPath);
	}

    /**
     * 载入语言包文件
     * @param $langPath
     * @throws \Tang\Exception\SystemException
     */
    protected function load($langPath)
	{
		if(!file_exists($langPath))
		{
			throw new SystemException('Language pack not found',array($langPath),20004);
		}
		$tmp = include_once $langPath;
		if(is_array($tmp))
		{
			$this->data = array_merge($this->data,$tmp);
		}
	}

    /**
     * 获取语言包目录
     * @param $name
     * @return string
     */
    protected function getPath($name)
	{
		return $this->language.DIRECTORY_SEPARATOR.$this->charset.DIRECTORY_SEPARATOR.ucfirst($name).'.php';
	}
}
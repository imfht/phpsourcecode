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
use Tang\Interfaces\ISetConfig;
use Tang\Web\Parameters;

interface IView extends ISetConfig
{
	public function display(Parameters $parameters,$viewType,$template='',$saveFilePath='',$isOutput=true);
	public function setTheme($theme);
	public function getTheme();
	public function assgin($key,$value);
	public function get($key);
	public function getShare($key);

	/**
	 * 注册共享变量
	 * @param $key
	 * @param $value
	 * @return mixed
	 */
	public function assginShare($key,$value);

	public function merginData(array $data);
	/**
	 * @return array
	 */
	public function getConfig();
}
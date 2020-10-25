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
 * 浏览器信息接口
 * Interface IBrowser
 * @package Tang\Web\Browser
 */
interface IBrowser
{
	/**
	 * 获取浏览器名称
	 * @return string
	 */
	public function getBrowserName();
	/**
	 * 获取浏览器版本号
	 * @return string
	 */
	public function getBrowserVersion();
	/**
	 * 获取语言
	 * @return string
	 */
	public function getLanguage();
	/**
	 * 获取user agent
	 * @return string
	 */
	public function getUserAgent();
	/**
	 * 获取是否支持ActiveX控件
	 * @return bool
	 */
	public function getSupportsActiveX();
	/**
	 * 获取平台
	 * @return string
	 */
	public function getPlatform();
}
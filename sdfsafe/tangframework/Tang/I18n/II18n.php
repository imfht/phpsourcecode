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
/**
 * i18n接口
 * @author 吉兵
 *
 */
interface II18n
{
	/**
	 * 获取语言
	 * 当$key包含->字符的时候，则为调用自定义的语言
	 * 例如
	 * user->user name is empty
	 * 则表示调用的user语言包的user name is empty项
	 * @param string $key
	 * @param array $args 为参数，和sprintf一样的意思
	 */
	public function get($key,array $args = array());
	/**
	 * 设置编码
	 * @param string $charset
	 */
	public function setCharset($charset);
	/**
	 * 设置语言类型
	 * @param string $language
	 */
	public function setLanguage($language);

	/**
	 * 设置应用程序路径
	 * @param $applicationDirectory
	 * @return mixed
	 */
	public function setApplicationDirectory($applicationDirectory);

	/**
	 * 载入模型语言
	 * @param string $modelName
	 */
	public function loadModelLang($modelName);
	/**
	 * 载入框架语言
	 */
	public function loadFrameworkLanguage();
}
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
namespace Tang\Web\Cookie;
use Tang\Interfaces\ISetConfig;

interface ICookie extends ISetConfig
{
	/**
	 * 设置一个Cookie
	 * @param string $name cookie名
	 * @param string $value cookie值
	 * @param string $expire 时效
	 * @param string $path 路径
	 * @param string $domain 域
	 */
	public function set($name,$value,$expire=-1,$path='',$domain='');
	/**
	 * 获得一个Cookie,如果没有值，则返回$default
	 * @param string $name
     * @param null $default
	 * @return string
	 */
	public function get($name,$default=null);
	/**
	 * 删除一个Cookie
	 * @param string $name
	 */
	public function delete($name);
	/**
	 * 清除cookie
	 */
	public function clean();
}
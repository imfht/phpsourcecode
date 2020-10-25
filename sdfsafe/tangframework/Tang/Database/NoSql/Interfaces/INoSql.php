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
namespace Tang\Database\NoSql\Interfaces;
use Tang\Interfaces\ISetConfig;

/**
 * Nosql实现接口
 * Interface INoSql
 * @package Tang\Database\NoSql\Interfaces
 */
interface INoSql extends ISetConfig
{
	/**
	 * 连接服务器
	 * @return mixed
	 */
	public function connect();

	/**
	 * 关闭
	 * @return mixed
	 */
	public function close();
}
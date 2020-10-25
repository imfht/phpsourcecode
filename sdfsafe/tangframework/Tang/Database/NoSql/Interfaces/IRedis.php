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
/**
 * Redis接口
 * Interface IRedis
 * @package Tang\Database\NoSql\Interfaces
 */
interface IRedis extends INoSql
{
	/**
	 * 获取写连接
	 * @return Redis
	 */
	public function getWriteRedis();

	/**
	 * 获取读连接
	 * @return Redis
	 */
	public function getReadRedis();
}
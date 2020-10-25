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
namespace Tang\Database\Sql\Connectors;
use Tang\Interfaces\ISetConfig;

/**
 * 数据库连接器接口
 * Interface IConnector
 * @package Tang\Database\Sql\Connectors
 */
interface IConnector extends ISetConfig
{
    /**
     * 连接
     * @return mixed
     */
    public function connect();
	/**
	 * 获取读PDO
	 * @return \PDO
	 */
	public function getReadPdo();
	/**
	 * 获取写PDO
	 * @return \PDO
	 */
	public function getWritePdo();
}
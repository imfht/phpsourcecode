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
namespace Tang\Database\Sql\Schema\Builders;
use Tang\Database\Sql\Schema\Grammar\Grammar;
use Tang\Database\Sql\Connections\Connection;
use Tang\Database\Sql\Schema\DDL;
use Closure;

/**
 * 表结构构建器
 * Class Builder
 * @package Tang\Database\Sql\Schema\Builders
 */
abstract class Builder
{
	/**
     * 数据库连接
     *
     * @access protected
     * @var Connection
     */
    protected $connection = null;
    /**
     * 解释器
     *
     * @access protected
     * @var Grammar
     */
    protected $grammar = null;

    /**
     * 传入数据库对象和解析对象
     * @param Connection $connection
     * @param Grammar $grammar
     */
    public function __construct(Connection $connection,Grammar $grammar)
    {
    	$this->connection = $connection;
    	$this->grammar = $grammar;
    }

    /**
     * 获取$tableName表的字段信息
     * @param $tableName
     * @return mixed
     */
    public function getColumns($tableName)
    {
    	$tableName = $this->connection->getTablePrefix().$tableName;
    	$results = $this->connection->select($this->grammar->compileGetColumn($tableName));
    	return $this->columnsHandle($results);
    }

    /**
     * 创建$tableName的结构
     * @param string $tableName
     * @param callable $callback $callback将得到DDL对象参数
     * @return DDL
     */
    public function createDDL($tableName,Closure $callback = null)
    {
    	$that = $this;
    	return new DDL($tableName,$callback);
    }

    /**
     * 根据$ddl执行语句
     * @param DDL $ddl
     */
    public function buildDDL(DDL $ddl)
    {
    	$ddl->build($this->connection, $this->grammar);
    }

    /**
     * 字段处理程序
     * @param $results
     * @return mixed
     */
    protected abstract function columnsHandle($results);
}
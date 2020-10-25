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
namespace Tang\Database\Sql\Connections;
use Tang\Database\Sql\Query\Grammar\Mysql as MysqlQueryGrammar;
use Tang\Database\Sql\Schema\Grammar\Mysql as MysqlSchemaGrammar;
use Tang\Database\Sql\Schema\Builders\Mysql as MysqlSchemaBuilder;

/**
 * Class Mysql
 * @package Tang\Database\Sql\Connections
 */
class Mysql extends Connection
{
    /**
     * @return \Tang\Database\Sql\Schema\Builders\Builder|MysqlSchemaBuilder
     */
    public function getSchemaBuilder()
	{
		return new MysqlSchemaBuilder($this, $this->getSchemaGrammar());
	}

    /**
     * @return \Tang\Database\Sql\Query\Grammar\Grammar|MysqlQueryGrammar
     */
    protected function defaultQueryGrammar()
	{
		$this->queryGrammar = new MysqlQueryGrammar();
		$this->queryGrammar->setTablePrefix($this->tablePrefix);
		return $this->queryGrammar;
	}

    /**
     * @return \Tang\Database\Sql\Schema\Grammar\Grammar|MysqlSchemaGrammar
     */
    protected function defaultSchemaGrammar()
	{
		$this->schemaGrammar = new MysqlSchemaGrammar();
		$this->schemaGrammar->setTablePrefix($this->tablePrefix);
		return $this->schemaGrammar;
	}
}
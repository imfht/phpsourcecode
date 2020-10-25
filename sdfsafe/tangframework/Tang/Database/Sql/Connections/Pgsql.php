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
use Tang\Database\Sql\Query\Grammar\Pgsql as PgsqlQueryGrammar;
use Tang\Database\Sql\Schema\Grammar\Pgsql as PgsqlSchemaGrammar;
use Tang\Database\Sql\Schema\Builders\Pgsql as PgsqlSchemaBuilder;

/**
 * Class Pgsql
 * @package Tang\Database\Sql\Connections
 */
class Pgsql extends Connection
{
    /**
     * @return \Tang\Database\Sql\Schema\Builders\Builder|PgsqlSchemaBuilder
     */
    public function getSchemaBuilder()
	{
		return new PgsqlSchemaBuilder($this, $this->getSchemaGrammar());
	}

    /**
     * @return \Tang\Database\Sql\Query\Grammar\Grammar|PgsqlQueryGrammar
     */
    protected function defaultQueryGrammar()
	{
		$this->queryGrammar = new PgsqlQueryGrammar();
		$this->queryGrammar->setTablePrefix($this->tablePrefix);
		return $this->queryGrammar;
	}

    /**
     * @return \Tang\Database\Sql\Schema\Grammar\Grammar|PgsqlSchemaGrammar
     */
    protected function defaultSchemaGrammar()
	{
		$this->schemaGrammar = new PgsqlSchemaGrammar();
		$this->schemaGrammar->setTablePrefix($this->tablePrefix);
		return $this->schemaGrammar;
	}
}
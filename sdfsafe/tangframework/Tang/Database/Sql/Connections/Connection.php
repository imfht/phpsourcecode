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
use Tang\Database\Sql\Query\Grammar\Grammar as QueryGrammar;
use Tang\Database\Sql\Query\Builder;
use Tang\Database\Sql\Exceptions\QueryException;
use Closure;
use Tang\Exception\SystemException;

/**
 * 数据库对象
 * Class Connection
 * @package Tang\Database\Sql\Connections
 */
abstract class Connection
{
    /**
     * 写的PDO对象
     * @var \PDO
     */
    protected $writePdo = null;

    /**
     * 读的PDO对象
     * @var \PDO
     */
    protected $readPdo = null;

    /**
     * Short description of attribute transactions
     *
     * @access public
     * @var int
     */
    protected $transactions = 0;

    /**
     * 配置文件
     * @var array
     */
    protected $config = array();

    /**
     * 表前缀
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * 查询解析器
     * @var QueryGrammar
     */
    protected $queryGrammar;

    /**
     * 结构解析器
     * @var \Tang\Database\Sql\Query\Grammar\Grammar
     */
    protected $schemaGrammar;

    /**
     * 数据库
     * @var string
     */
    protected $database;

    /**
     * @param \PDO $writePdo
     * @param \PDO $readPdo
     * @param $database
     * @param string $tablePrefix
     * @param array $config
     */
    public function __construct(\PDO $writePdo,\PDO $readPdo,$database,$tablePrefix='',$config = array())
    {
        $this->writePdo = $writePdo;
        $this->readPdo = $readPdo;
        $this->tablePrefix = $tablePrefix;
        $this->database = $database;
        $this->config = $config;
    }
    /**
     * 获取查询解析器
     * @return \Tang\Database\Sql\Query\Grammar\Grammar
     */
	public function getQueryGrammar()
	{
		return $this->queryGrammar ? $this->queryGrammar:$this->defaultQueryGrammar();
	}

    /**
     * 获取结构解析器
     * @return \Tang\Database\Sql\Schema\Grammar\Grammar
     */
    public function getSchemaGrammar()
	{
		return $this->schemaGrammar?$this->schemaGrammar:$this->defaultSchemaGrammar();
	}

	/**
	 * 获取tableName的表查询语句构建器
     * <code>
     * $query = DB::get()->table('user');
     * //插入
     * $query->insert(array('userName'=>'xx','password'=>md5('xxx')));
     * //查询
     * print_r($query->find(1));
     * </code>
	 * @param string $tableName
	 * @return \Tang\Database\Sql\Query\Builder
	 */
	public function table($tableName)
	{
		$query = new Builder($this, $this->getQueryGrammar());
		return $query->setTable($tableName);
	}

    /**
     * 查询SQL语句
     * <code>
     * $result = DB::get()->select('select * from table where x=?',array(1));
     * </code>
     * @param string $sql 查询语句
     * @param array $bindings 绑定参数
     * @param mixed $index 索引
     * @return mixed
     */
    public function select($sql,array $bindings = array(),$index = '')
    {
        return $this->run($sql, $bindings, function($me, $sql, $bindings) use($index)
		{
			$statement = $me->readPdo->prepare($sql);
			$statement->execute($bindings);
            if($index)
            {
                $resultList = array();
                while (($row = $statement->fetch(\PDO::FETCH_ASSOC)) != false)
                {
                    isset($row[$index]) ? $resultList[$row[$index]] = $row : $resultList[] = $row;
                }
                return $resultList;
            } else
            {
                return $statement->fetchAll(\PDO::FETCH_ASSOC);
            }
		});
    }

    /**
     * 执行SQL语句
     * <code>
     * DB::get()->statement('insert into xtable values(?,?,?)',array(1,2,3));
     * </code>
     * @param string $query 查询语句
     * @param array $bindings 绑定参数
     * @return mixed
     */
    public function statement($query,array $bindings = array())
    {
    	return $this->run($query, $bindings, function($me, $query, $bindings)
    	{
    		return $me->writePdo->prepare($query)->execute($bindings);
    	});
    }

    /**
     * 开启事务
     * 不用人为的去commit rollBack
     * 当整个$callback没有发生异常时，则commit否则rollBack
     * <code>
     * DB::get()->transaction(function($db)
     * {
     *  $db->statement('insert into xtable values(?,?,?)',array(1,2,3));
     *  $id = $db->getWritePdo()->lastInsertId();
     * $db->statement('insert into xtable2 values(?,?,?)',array($id,2,3));
     * })
     * </code>
     * @param callable $callback 事务回调，传递参数为本对象
     * @return mixed
     * @throws \Exception
     * @throws \Tang\Database\Sql\Exceptions\QueryException
     * @throws \Tang\Exception\SystemException
     */
    public function transaction(Closure $callback)
    {
    	$this->beginTransaction();
    	try
    	{
    		$result = $callback($this);
    		$this->commit();
    	} catch (QueryException $e)
    	{
    		$this->rollBack();
    		throw $e;
    	} catch (\Exception $e)
    	{
    		$this->rollBack();
    		throw new SystemException($e->getMessage(),array(),40002,'SQL');
    	} 
    	return $result;
    }

    /**
     * 开启事务
     */
    public function beginTransaction()
    {
    	++$this->transactions;
		if ($this->transactions == 1)
		{
			$this->writePdo->beginTransaction();
		}
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        if ($this->transactions == 1) $this->writePdo->commit();
		--$this->transactions;
    }

    /**
     * 回滚
     */
    public function rollBack()
    {
    	if ($this->transactions == 1)
		{
			$this->transactions = 0;
			$this->writePdo->rollBack();
		}else
		{
			--$this->transactions;
		}
    }

    /**
     * 获取配置
     * @return array
     */
    public function getConfig()
    {
    	return $this->config;
    }

    /**
     * 获取写PDO
     * @return \PDO
     */
    public function getWritePdo()
	{
		return $this->writePdo;
	}

    /**
     * 获取表前缀
     * @return string
     */
    public function getTablePrefix()
    {
    	return $this->tablePrefix;
    }

    /**
     * 设置数据库
     * @param $database
     */
    public function setDatabase($database)
    {
    	$this->database = $database;
    }

    /**
     * 获取数据库
     * @return string
     */
    public function getDatabase()
    {
    	return $this->database;
    }

    /**
     * 运行sql
     * @param $sql
     * @param $bindings
     * @param callable $callback
     * @return mixed
     * @throws \Tang\Database\Sql\Exceptions\QueryException
     */
    protected function run($sql, $bindings, \Closure $callback)
    {
    	$bindings = $this->prepareBindings($bindings);
    	try
    	{
    		$result = $callback($this, $sql, $bindings);
    	}
    	catch (\Exception $e)
    	{
    		throw new QueryException($e->getMessage(),$sql,$bindings);
    	}
    	return $result;
    }

    /**
     * 处理绑定参数
     * @param array $bindings
     * @return array
     */
    public function prepareBindings(array $bindings)
    {
    	if(!$bindings)
    	{
    		return $bindings;
    	}
    	$grammar = $this->getQueryGrammar();
    	foreach ($bindings as $key => $value)
    	{
    		if ($value instanceof \DateTime)
    		{
    			$bindings[$key] = $value->format($grammar->getDateFormat());
    		}
    		elseif ($value === false)
    		{
    			$bindings[$key] = 0;
    		}
    	}
    	return $bindings;
    }

    /**
     * 默认的查询解析器
     * @return QueryGrammar
     */
    abstract protected function defaultQueryGrammar();

    /**
     * 默认的结构解析器
     * @return \Tang\Database\Sql\Schema\Grammar\Grammar
     */
    abstract protected function defaultSchemaGrammar();

    /**
     * 获取Schema
     * @return \Tang\Database\Sql\Schema\Builders\Builder
     */
    abstract public function getSchemaBuilder();
}
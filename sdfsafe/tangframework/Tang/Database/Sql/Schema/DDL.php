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
namespace Tang\Database\Sql\Schema;
use Closure;
use Tang\Database\Sql\Connections\Connection;
use Tang\Database\Sql\Schema\Grammar\Grammar;

/**
 * 外键约束选项
 * Class ForeignOptions
 * @package Tang\Database\Sql\Schema
 */
class ForeignOptions
{
	const NO_ACTION = 'NO ACTION';
	const RESTRICT = 'RESTRICT';
	const CASCADE = 'CASCADE';
	const SET_NULL = 'SET NULL';
}
/**
 * ddl类
 * 用于修改 创建 表
 * @package Tang\Database\Sql\Schema
 */
class DDL
{
    /**
     * 表名
     * @var string
     */
    protected $tableName;
    /**
     * 命令
     * @var array
     */
    protected $commands = array();
	/**
	 * 引擎类型
	 * @var unknown
	 */
	protected $engine;
    /**
     * 字段列表
     * @var array
     */
    protected $columns = array();

    /**
     * 创建$tableName
     * 如果有$callback则调用$callback并且传递DDL对象
     * @param $tableName
     * @param callable $callback
     */
    public function __construct($tableName,Closure $callback = null)
	{
		$this->tableName = $tableName;
		if($callback)
		{
			$callback($this);
		}
	}

    /**
     * 表名
     * @param Connection $connection
     */
    public function build(Connection $connection)
	{
		foreach ($this->toSql($connection) as $statement)
		{
			$connection->statement($statement);
		}
	}

    /**
     * 构建语句转换SQL数组
     * @param Connection $connection
     * @return array
     */
    public function toSql(Connection $connection)
	{
		$this->addImpliedCommands();
		$statements = array();
		foreach ($this->commands as $command)
		{
            $grammar = $connection->getSchemaGrammar();
			$method = 'compile'.ucfirst($command['name']);
			if (method_exists($grammar,$method))
			{
				if (!is_null($sql = $grammar->$method($this,$command,$connection)))
				{
					$statements = array_merge($statements, (array)$sql);
				}
			}
		}
		return $statements;
	}

    /**
     * 创建表
     * @return array
     */
    public function create()
	{
		return $this->addCommand('create');
	}

    /**
     * 设置引擎
     * @param $engine
     */
    public function setEngine($engine)
	{
		$this->engine = $engine;
	}

    /**
     * 获取引擎
     * @return unknown
     */
    public function getEngine()
	{
		return $this->engine;
	}

    /**
     * 获取字段
     * @return array
     */
    public function getColumns()
	{
		return $this->columns;
	}

    /**
     * 获取表名
     * @return string
     */
    public function getTableName()
	{
		return $this->tableName;
	}

    /**
     * 删除表
     */
    public function drop()
	{
		$this->addCommand('drop');
	}

    /**
     *  如果表存在删除表
     */
    public function dropIfExists()
	{
		$this->addCommand('dropIfExists');
	}

    /**
     * 删除字段
     * <code>
     * $ddl->dropColumns('userId');//删除userId字段
     * $ddl->dropColumns('userId','userName','password');//删除userId userName password字段
     * $ddl->dropColumns(array('userId','userName','password'));//删除userId userName password字段
     * </code>
     * @param $columns
     * @return array
     */
    public function dropColumns($columns)
	{
		$columns = is_array($columns) ? $columns : (array) func_get_args();
		return $this->addCommand('dropColumns', array('columns'=>$columns));
	}

    /**
     * 重命名字段
     * @param $srcColumn 原字段名
     * @param $descColumn 新字段名
     * @return array
     */
    public function renameColumn($srcColumn, $descColumn)
	{
		return $this->addCommand('renameColumn', compact('srcColumn', 'descColumn'));
	}

    /**
     * 删除主键
     * @param null $index
     * @return array
     */
    public function dropPrimary($index = null)
	{
		return $this->dropIndexCommand('dropPrimary', 'primary', $index);
	}
    /**
     * 删除unique索引
     * @param $index
     * @return array
     */
    public function dropUnique($index)
	{
		return $this->dropIndexCommand('dropUnique', 'unique', $index);
	}

    /**
     * 删除索引
     * @param $index
     * @return array
     */
    public function dropIndex($index)
	{
		return $this->dropIndexCommand('dropIndex', 'index', $index);
	}

    /**
     * 删除外键
     * @param $index
     * @return array
     */
    public function dropForeign($index)
	{
		return $this->dropIndexCommand('dropForeign', 'foreign', $index);
	}

    /**
     * 冲命名表
     * @param $newTableName 新表名
     * @return array
     */
    public function rename($newTableName)
	{
		return $this->addCommand('rename', compact('newTableName'));
	}

    /**
     * 将$columns设置主键
     * @param $columns
     * @param null $name 索引名称 为空的话则系统自动生成
     * @return array
     */
    public function primary($columns, $name = null)
	{
		return $this->indexCommand('primary', $columns, $name);
	}

    /**
     * 将$columns设置唯一索引
     * @param $columns
     * @param null $name 索引名称 为空的话则系统自动生成
     * @return array
     */
    public function unique($columns, $name = null)
	{
		return $this->indexCommand('unique', $columns, $name);
	}

    /**
     * 将$columns设置为索引
     * @param $columns
     * @param null $name 索引名称 为空的话则系统自动生成
     * @return array
     */
    public function index($columns, $name = null)
	{
		return $this->indexCommand('index', $columns, $name);
	}

    /**
     * 设置外键
     * @param $column 本表字段
     * @param $onTable 外键表
     * @param $onColumn 外键字段
     * @param null $onDelete  onDelete约束
     * @param null $onUpdate onUpdate约束
     * @param null $name 名称，为空的时候系统自动生成
     * @return array
     */
    public function foreign($column,$onTable,$onColumn,$onDelete=null,$onUpdate=null, $name = null)
	{
		if (is_null($name))
		{
			$name = $this->createIndexName('foreign', array($column,$onTable,$onColumn));
		}
		$index = $name;
		return $this->addCommand('foreign', compact('index', 'column','onColumn','onTable','onDelete','onUpdate'));
	}

    /**
     * 增加unsigned int自动增长键
     * @param int $column
     * @return array
     */
    public function increments($column)
	{
		return $this->unsignedInteger($column, true);
	}

	/**
	 * 增加bigint自动增长键
	 * @param string $column
     * @return array
	 */
	public function bigIncrements($column)
	{
		return $this->unsignedBigInteger($column, true);
	}

	/**
	 * 增加定长字符串字段
	 * @param string $column
	 * @param int $length
     * @return array
	 */
    public function char($column, $length = 255)
	{
		return $this->addColumn('char', $column, compact('length'));
	}

	/**
	 * 增加可变字符串字段
	 * @param string $column
	 * @param int $length
     * @return array
	 */
	public function string($column, $length = 255)
	{
		return $this->addColumn('string', $column, compact('length'));
	}

	/**
	 * 增加文本字段
	 * @param string $column
     * @return array
	 */
	public function text($column)
	{
		return $this->addColumn('text', $column);
	}

    /**
     * 增加medium text字段
     * @param $column
     * @return array
     */
    public function mediumText($column)
	{
		return $this->addColumn('mediumText', $column);
	}
    /**
     * 增加long text字段
     * @param $column
     * @return array
     */
	public function longText($column)
	{
		return $this->addColumn('longText', $column);
	}

    /**
     * 增加int字段
     * @param $column 字段名
     * @param bool $autoIncrement 是否字段增长
     * @param bool $unsigned 是否为无符号
     * @return array
     */
    public function integer($column, $autoIncrement = false, $unsigned = false)
	{
		return $this->addColumn('integer', $column, compact('autoIncrement', 'unsigned'));
	}

    /**
     * 增加big int字段
     * @param $column 字段名
     * @param bool $autoIncrement 是否字段增长
     * @param bool $unsigned 是否为无符号
     * @return array
     */
    public function bigInteger($column, $autoIncrement = false, $unsigned = false)
	{
		return $this->addColumn('bigInteger', $column, compact('autoIncrement', 'unsigned'));
	}

    /**
     * 增加medium int字段
     * @param $column 字段名
     * @param bool $autoIncrement 是否字段增长
     * @param bool $unsigned 是否为无符号
     * @return array
     */
	public function mediumInteger($column, $autoIncrement = false, $unsigned = false)
	{
		return $this->addColumn('mediumInteger', $column, compact('autoIncrement', 'unsigned'));
	}

    /**
     * 增加tinyInt int字段
     * @param $column 字段名
     * @param bool $autoIncrement 是否字段增长
     * @param bool $unsigned 是否为无符号
     * @return array
     */
	public function tinyInteger($column, $autoIncrement = false, $unsigned = false)
	{
		return $this->addColumn('tinyInteger', $column, compact('autoIncrement', 'unsigned'));
	}

    /**
     * 增加small int字段
     * @param $column 字段名
     * @param bool $autoIncrement 是否字段增长
     * @param bool $unsigned 是否为无符号
     * @return array
     */
	public function smallInteger($column, $autoIncrement = false, $unsigned = false)
	{
		return $this->addColumn('smallInteger', $column, compact('autoIncrement', 'unsigned'));
	}

    /**
     * 增加无符号 int字段
     * @param $column 字段名
     * @param bool $autoIncrement 是否字段增长
     * @return array
     */
	public function unsignedInteger($column, $autoIncrement = false)
	{
		return $this->integer($column, $autoIncrement, true);
	}

    /**
     * 增加无符号 big int字段
     * @param string $column 字段名
     * @param bool $autoIncrement 是否字段增长
     * @return array
     */
	public function unsignedBigInteger($column, $autoIncrement = false)
	{
		return $this->bigInteger($column, $autoIncrement, true);
	}


    /**
     * 增加float字段
     * @param string $column 字段名
     * @param int $total
     * @param int $places
     * @return array
     */
    public function float($column, $total = 8, $places = 2)
	{
		return $this->addColumn('float', $column, compact('total', 'places'));
	}

    /**
     * 增加double字段
     * @param string $column 字段名
     * @param int $total
     * @param int $places
     * @return array
     */
	public function double($column, $total = null, $places = null)
	{
		return $this->addColumn('double', $column, compact('total', 'places'));
	}

    /**
     * 增加decimal字段
     * @param string $column 字段名
     * @param int $total
     * @param int $places
     * @return array
     */
	public function decimal($column, $total = 8, $places = 2)
	{
		return $this->addColumn('decimal', $column, compact('total', 'places'));
	}

    /**
     * 增加bool字段
     * @param string $column
     * @return array
     */
    public function boolean($column)
	{
		return $this->addColumn('boolean', $column);
	}

    /**
     * 增加枚举类型字段
     * @param string $column
     * @param array $values
     * @return array
     */
    public function enum($column, array $values)
	{
		return $this->addColumn('enum', $column, compact('values'));
	}

    /**
     * 增加date字段
     * @param string $column
     * @return array
     */
	public function date($column)
	{
		return $this->addColumn('date', $column);
	}

    /**
     * 增加dateTime字段
     * @param string $column
     * @return array
     */
	public function dateTime($column)
	{
		return $this->addColumn('dateTime', $column);
	}

    /**
     * 增加dateTime字段
     * @param string $column
     * @return array
     */
	public function time($column)
	{
		return $this->addColumn('time', $column);
	}

    /**
     * 增加timestamp字段
     * @param string $column
     * @return array
     */
	public function timestamp($column)
	{
		return $this->addColumn('timestamp', $column);
	}

    /**
     * 增加binary字段
     * @param string $column
     * @return array
     */
	public function binary($column)
	{
		return $this->addColumn('binary', $column);
	}
	public function morphs($name)
	{
		$this->unsignedInteger($name.'Id');
	
		$this->string($name.'Type');
	
		$this->index(array(name.'Id',$name.'Type'));
	}

    /**
     * 增加命令
     * @param $name
     * @param array $parameters
     * @return array
     */
    protected function addCommand($name, array $parameters = array())
	{
		$parameters['name'] = $name;
		$this->commands[] = $parameters;
		return $parameters;
	}

    /**
     * 删除索引命令
     * @param $command
     * @param $type
     * @param $index
     * @return array
     */
    protected function dropIndexCommand($command,$type,$index)
	{
		$columns = array();
		if (is_array($index))
		{
			$columns = $index;
			$index = $this->createIndexName($type, $columns);
		}
		return $this->indexCommand($command, $columns, $index);
	}

    /**
     * 增加索引
     * @param $type
     * @param $columns
     * @param $index
     * @return array
     */
    protected function indexCommand($type,$columns, $index)
	{
		$columns = (array) $columns;
		if (is_null($index))
		{
			$index = $this->createIndexName($type,$columns);
		}
		return $this->addCommand($type, compact('index', 'columns'));
	}

    /**
     * 创建index索引
     * @param $type
     * @param array $columns
     * @return mixed
     */
    protected function createIndexName($type,array $columns)
	{
		$columns = array_map(function($value){return ucfirst($value);}, $columns);
		$index = lcfirst($this->tableName);
		$index .= implode('', $columns).ucfirst($type);
		return str_replace(array('-', '.'), '', $index);
	}

    /**
     * 增加字段
     * @param $type 类型
     * @param $name 字段名
     * @param array $parameters 参数
     * @return array
     */
    protected function addColumn($type, $name, array $parameters = array())
	{
		$parameters['type'] = $type;
		$parameters['name'] = $name;
		$this->columns[] = $parameters;
		return $parameters;
	}

    /**
     * 添加add命令
     * 并为字段添加索引命令
     */
    protected function addImpliedCommands()
	{
		if (count($this->columns) > 0 && ! $this->creating())
		{
			array_unshift($this->commands, array('name'=>'add'));
		}
		$this->addFluentIndexes();
	}

    /**
     * 循环字段添加索引命令
     */
    protected function addFluentIndexes()
	{
		foreach ($this->columns as $column)
		{
			foreach (array('primary', 'unique', 'index') as $index)
			{
				if (isset($column[$index]) && $column[$index] === true)
				{
					$this->$index($column['name']);	
					continue 2;
				}elseif (isset($column[$index]))
				{
					$this->$index($column['name'], $column[$index]);
					continue 2;
				}
			}
		}
	}

    /**
     * 是否在创建
     * @return bool
     */
    protected function creating()
	{
		foreach ($this->commands as $command)
		{
			if ($command['name'] == 'create') return true;
		}
		return false;
	}
}
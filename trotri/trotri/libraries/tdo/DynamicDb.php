<?php
/**
 * Trotri Data Objects
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace tdo;

/**
 * DynamicDb abstract class file
 * 动态数据库操作基类，自动读取并缓存表结构，自动组建并执行SQL语句
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: DynamicDb.php 1 2013-05-18 14:58:59Z huan.song $
 * @package tdo
 * @since 1.0
 */
abstract class DynamicDb extends AbstractDb
{
    /**
     * @var instance of tfc\db\TableSchema
     */
    protected $_tableSchema = null;

    /**
     * @var string 表名
     */
    protected $_tableName;

    /**
     * @var string 被引用的表名，可以放在SQL命令中执行
     */
    protected $_quoteTableName;

    /**
     * @var array|string 主键名
     */
    protected $_primaryKey;

    /**
     * @var array|string 被引用的主键名，可以放在SQL命令中执行
     */
    protected $_quotePrimaryKey;

    /**
     * 构造方法：初始化表名
     * @param string $tableName
     */
    public function __construct($tableName)
    {
        $this->setTableName($tableName);
    }

    /**
     * 通过主键，获取某个列的值。如果是联合主键，则参数是数组，且数组中值的顺序必须和PRIMARY KEY (pk1, pk2)中的顺序相同
     * @param string $columnName
     * @param array|integer $value
     * @return mixed
     */
    public function getByPk($columnName, $value)
    {
        return $this->getByCondition($columnName, $this->getPKCondition(), $value);
    }

    /**
     * 通过主键，查询一条记录。如果是联合主键，则参数是数组，且数组中值的顺序必须和PRIMARY KEY (pk1, pk2)中的顺序相同
     * @param array|integer $value
     * @return array
     */
    public function findByPk($value)
    {
        return $this->findByCondition($this->getPKCondition(), $value);
    }

    /**
     * 通过多个字段名和值，获取主键的值，字段之间用简单的AND连接。不支持联合主键
     * @param array $attributes
     * @return mixed
     */
    public function getPkByAttributes(array $attributes = array())
    {
        $condition = $this->getCommandBuilder()->createAndCondition(array_keys($attributes));
        return $this->getPkByCondition($condition, $attributes);
    }

    /**
     * 通过多个字段名和值，获取某个列的值，字段之间用简单的AND连接
     * @param string $columnName
     * @param array $attributes
     * @return mixed
     */
    public function getByAttributes($columnName, array $attributes = array())
    {
        $condition = $this->getCommandBuilder()->createAndCondition(array_keys($attributes));
        return $this->getByCondition($columnName, $condition, $attributes);
    }

    /**
     * 通过多个字段名和值，查询多条记录，字段之间用简单的AND连接
     * @param array $attributes
     * @param string $order
     * @param integer $limit
     * @param integer $offset
     * @param string $option
     * @return array
     */
    public function findAllByAttributes(array $attributes = array(), $order = '', $limit = 0, $offset = 0, $option = '')
    {
        $condition = $this->getCommandBuilder()->createAndCondition(array_keys($attributes));
        return $this->findAllByCondition($condition, $attributes, $order, $limit, $offset, $option);
    }

    /**
     * 通过多个字段名和值，查询两个字段记录，字段之间用简单的AND连接，并且以键值对方式返回
     * @param array $columnNames
     * @param array $attributes
     * @param string $order
     * @param integer $limit
     * @param integer $offset
     * @return array
     */
    public function findPairsByAttributes(array $columnNames, array $attributes = array(), $order = '', $limit = 0, $offset = 0)
    {
        $condition = $this->getCommandBuilder()->createAndCondition(array_keys($attributes));
        return $this->findPairsByCondition($columnNames, $condition, $attributes, $order, $limit, $offset);
    }

    /**
     * 通过条件，查询多条记录，只查询指定的字段
     * @param array $columnNames
     * @param string $attributes
     * @param string $order
     * @param integer $limit
     * @param integer $offset
     * @param string $option
     * @return array
     */
    public function findColumnsByAttributes(array $columnNames, array $attributes = array(), $order = '', $limit = 0, $offset = 0, $option = '')
    {
        $condition = $this->getCommandBuilder()->createAndCondition(array_keys($attributes));
        return $this->findColumnsByCondition($columnNames, $condition, $attributes, $order, $limit, $offset, $option);
    }

    /**
     * 通过多个字段名和值，统计记录数，字段之间用简单的AND连接
     * @param array $attributes
     * @return integer
     */
    public function countByAttributes(array $attributes = array())
    {
        $condition = $this->getCommandBuilder()->createAndCondition(array_keys($attributes));
        return $this->countByCondition($condition, $attributes);
    }

    /**
     * 通过多个字段名和值，查询一条记录，字段之间用简单的AND连接
     * @param array $attributes
     * @return array
     */
    public function findByAttributes(array $attributes = array())
    {
        $condition = $this->getCommandBuilder()->createAndCondition(array_keys($attributes));
        return $this->findByCondition($condition, $attributes);
    }

    /**
     * 通过条件，获取主键的值。不支持联合主键
     * @param string $condition
     * @param mixed $params
     * @return mixed
     */
    public function getPkByCondition($condition, $params = null)
    {
        return $this->getByCondition($this->getTableSchema()->primaryKey, $condition, $params);
    }

    /**
     * 通过条件，获取某个列的值
     * @param string $columnName
     * @param string $condition
     * @param mixed $params
     * @return mixed
     */
    public function getByCondition($columnName, $condition, $params = null)
    {
        $sql = $this->getCommandBuilder()->createFind($this->getTableSchema()->name, array($columnName), $condition);
        return $this->fetchColumn($sql, $params);
    }

    /**
     * 获取表中所有的记录
     * @param string $order
     * @param integer $limit
     * @param integer $offset
     * @param string $option
     * @return array
     */
    public function findAll($order = '', $limit = 0, $offset = 0, $option = '')
    {
        return $this->findAllByCondition(1, null, $order, $limit, $offset, $option);
    }

    /**
     * 通过条件，查询多条记录
     * @param string $condition
     * @param mixed $params
     * @param string $order
     * @param integer $limit
     * @param integer $offset
     * @param string $option
     * @return array
     */
    public function findAllByCondition($condition, $params = null, $order = '', $limit = 0, $offset = 0, $option = '')
    {
        $sql = $this->getCommandBuilder()->createFind($this->getTableSchema()->name, $this->getTableSchema()->columnNames, $condition, $order, $limit, $offset, $option);
        $funcName = ($option === 'SQL_CALC_FOUND_ROWS') ? 'fetchAllNoCache' : 'fetchAll';
        return $this->$funcName($sql, $params);
    }

    /**
     * 通过条件，查询两个字段记录，并且以键值对方式返回
     * @param array $columnNames
     * @param string $condition
     * @param mixed $params
     * @param string $order
     * @param integer $limit
     * @param integer $offset
     * @return array
     */
    public function findPairsByCondition(array $columnNames, $condition, $params = null, $order = '', $limit = 0, $offset = 0)
    {
        $sql = $this->getCommandBuilder()->createFind($this->getTableSchema()->name, $columnNames, $condition, $order, $limit, $offset);
        return $this->fetchPairs($sql, $params);
    }

    /**
     * 通过条件，查询多条记录，只查询指定的字段
     * @param array $columnNames
     * @param string $condition
     * @param mixed $params
     * @param string $order
     * @param integer $limit
     * @param integer $offset
     * @param string $option
     * @return array
     */
    public function findColumnsByCondition(array $columnNames, $condition, $params = null, $order = '', $limit = 0, $offset = 0, $option = '')
    {
        $sql = $this->getCommandBuilder()->createFind($this->getTableSchema()->name, $columnNames, $condition, $order, $limit, $offset, $option);
        $funcName = ($option === 'SQL_CALC_FOUND_ROWS') ? 'fetchAllNoCache' : 'fetchAll';
        return $this->$funcName($sql, $params);
    }

    /**
     * 通过条件，统计记录数
     * @param string $condition
     * @param mixed $params
     * @return integer
     */
    public function countByCondition($condition, $params = null)
    {
        $sql = $this->getCommandBuilder()->createCount($this->getTableSchema()->name, $condition);
        return $this->fetchColumn($sql, $params);
    }

    /**
     * 通过条件，查询一条记录
     * @param string $condition
     * @param mixed $params
     * @return array
     */
    public function findByCondition($condition, $params = null)
    {
        $sql = $this->getCommandBuilder()->createFind($this->getTableSchema()->name, $this->getTableSchema()->columnNames, $condition);
        return $this->fetch($sql, $params);
    }

    /**
     * 新增一条记录
     * @param array $attributes
     * @param boolean $ignore
     * @return integer
     */
    public function create(array $attributes = array(), $ignore = false)
    {
        $sql = $this->getCommandBuilder()->createInsert($this->getTableSchema()->name, array_keys($attributes), $ignore);
        return $this->insert($sql, $attributes);
    }

    /**
     * 通过主键，编辑一条记录。如果是联合主键，则参数是数组，且数组中值的顺序必须和PRIMARY KEY (pk1, pk2)中的顺序相同
     * @param integer|array $value
     * @param array $attributes
     * @return integer
     */
    public function modifyByPk($value, array $attributes = array())
    {
        $sql = $this->getCommandBuilder()->createUpdate($this->getTableSchema()->name, array_keys($attributes), $this->getPKCondition());
        $attributes = array_merge($attributes, array_values((array) $value));
        return $this->update($sql, $attributes);
    }

    /**
     * 通过主键，删除一条记录。如果是联合主键，则参数是数组，且数组中值的顺序必须和PRIMARY KEY (pk1, pk2)中的顺序相同
     * @param integer|array $value
     * @return integer
     */
    public function removeByPk($value)
    {
        $sql = $this->getCommandBuilder()->createDelete($this->getTableSchema()->name, $this->getPKCondition());
        return $this->delete($sql, $value);
    }

    /**
     * 通过主键，编辑多条记录。不支持联合主键
     * @param string $value
     * @param array $attributes
     * @return integer
     */
    public function batchModifyByPk($value, array $attributes = array())
    {
        $condition = $this->getQuotePrimaryKey() . ' IN (' . $value . ')';
        $sql = $this->getCommandBuilder()->createUpdate($this->getTableSchema()->name, array_keys($attributes), $condition);
        return $this->update($sql, $attributes);
    }

    /**
     * 通过主键，删除多条记录。不支持联合主键
     * @param string $value
     * @return integer
     */
    public function batchRemoveByPk($value)
    {
        $condition = $this->getQuotePrimaryKey() . ' IN (' . $value . ')';
        $sql = $this->getCommandBuilder()->createDelete($this->getTableSchema()->name, $condition);
        return $this->delete($sql);
    }

    /**
     * 新增一条记录，如果记录存在则编辑
     * @param array $attributes
     * @return integer
     */
    public function replace(array $attributes = array())
    {
        $sql = $this->getCommandBuilder()->createReplace($this->getTableSchema()->name, array_keys($attributes));
        return $this->update($sql, $attributes);
    }

    /**
     * 通过过滤数组，只保留表中含有的字段名
     * 如果是为INSERT、UPDATE服务，可以设置$autoIncrement为true，会清理掉自增字段
     * @param array $attributes
     * @param boolean $autoIncrement
     * @return void
     */
    public function filterAttributes(array &$attributes = array(), $autoIncrement = true)
    {
        $tableSchema = $this->getTableSchema();
        foreach ($attributes as $columnName => $value) {
            if (!$tableSchema->hasColumn($columnName)) {
                unset($attributes[$columnName]);
            }
        }

        if ($autoIncrement && $tableSchema->autoIncrement !== null && isset($attributes[$tableSchema->autoIncrement])) {
            unset($attributes[$tableSchema->autoIncrement]);
        }
    }

    /**
     * 通过主键名，获取Where条件
     * @return array|string
     */
    public function getPKCondition()
    {
        static $condition = null;
        if ($condition === null) {
            $primaryKey = $this->getQuotePrimaryKey();
            if (is_array($primaryKey)) {
                $primaryKey = implode(' = ' . CommandBuilder::PLACE_HOLDERS . ' AND ', $primaryKey);
            }

            $condition = $primaryKey . ' = ' . CommandBuilder::PLACE_HOLDERS;
        }

        return $condition;
    }

    /**
     * 获取表名
     * @return string
     */
    public function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * 设置表名
     * @param string $tableName
     * @return \tdo\DynamicDb
     */
    public function setTableName($tableName)
    {
        $tableName = strtolower(trim($tableName));
        $tblPrefix = $this->getTblprefix();
        if ($tblPrefix !== '' && stripos($tableName, $tblPrefix) !== 0) {
            $tableName = $tblPrefix . $tableName;
        }

        $this->_tableName = $tableName;
        return $this;
    }

    /**
     * 获取被引用的表名，可以放在SQL命令中执行
     * @return string
     */
    public function getQuoteTableName()
    {
        if ($this->_quoteTableName === null) {
            $this->_quoteTableName = $this->getCommandBuilder()->quoteTableName($this->getTableName());
        }

        return $this->_quoteTableName;
    }

    /**
     * 获取表的主键名
     * @return array|string
     */
    public function getPrimaryKey()
    {
        if ($this->_primaryKey === null) {
            $this->_primaryKey = $this->getTableSchema()->primaryKey;
        }

        return $this->_primaryKey;
    }

    /**
     * 获取被引用的主键名，可以放在SQL命令中执行
     * @return array|string
     */
    public function getQuotePrimaryKey()
    {
        if ($this->_quotePrimaryKey === null) {
            $primaryKey = $this->getPrimaryKey();
            $func = is_array($primaryKey) ? 'quoteColumnNames' : 'quoteColumnName';
            $this->_quotePrimaryKey = $this->getCommandBuilder()->$func($primaryKey);
        }

        return $this->_quotePrimaryKey;
    }

    /**
     * 通过表的实体类，获取表的概要描述，包括：表名、主键、自增字段、字段、默认值
     * 应该根据不同的数据库类型创建对应的TableSchema类：$dbType = $this->getDriver(false)->getDbType();
     * 这里只用到MySQL数据库，暂时不做对应多数据库类型
     * @return \tfc\db\TableSchema
     */
    public function getTableSchema()
    {
        if ($this->_tableSchema === null) {
            $entityBuilder = EntityBuilder::getInstance($this->getDbProxy());
            $this->_tableSchema = $entityBuilder->getTableSchema($this->getTableName());
        }

        return $this->_tableSchema;
    }
}

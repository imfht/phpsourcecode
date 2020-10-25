<?php

namespace Mysql;

/**
 * 数据读取模型抽象类
 *
 * @package Mysql
 */
abstract class AbstractModel {

    /**
     * ResultSet返回类型
     */
    const RESULT_SET_RETURN_TYPE_ARRAY = 'array';

    /**
     * 表名
     * 
     * @var string
     */
    protected $_tableName = null;

    /**
     * 表的主键名
     * 
     * @var type 
     */
    protected $_primaryKey = "id";

    /**
     * 事务开启计数器
     * mysql本身不支持嵌套事务,这里使用计数器来支持嵌套事务
     * 要求事务有begin就一定要有commit或者rollback
     */
    static $_transactionCounter = 0;

    /**
     * Zend 的适配器
     * 
     * @var \Zend\Db\Adapter\Adapter
     */
    static $dbAdapter = null;

    /**
     * 返回 Zend 的适配器
     * 
     * @return \Zend\Db\Adapter\Adapter
     */
    public function _getAdapter() {
        if (!self::$dbAdapter) {
            $conf = \Yaf\Registry::get('config')->get('resources.database.params');
            if (!$conf) {
                throw new \Exception('数据库连接必须设置');
            }
            self::$dbAdapter = new \Zend\Db\Adapter\Adapter($conf->toArray());
        }

        return self::$dbAdapter;
    }

    /**
     * 返回Zend的TableGateway
     * 
     * @return \Zend\Db\TableGateway\TableGateway
     */
    protected function _getDbTableGateway() {
        $resultSet    = new \Zend\Db\ResultSet\ResultSet(self::RESULT_SET_RETURN_TYPE_ARRAY);
        $tableGateway = new \Zend\Db\TableGateway\TableGateway($this->_tableName, $this->_getAdapter(), null, $resultSet);
        return $tableGateway;
    }

    /**
     * 返回Zend的Select对象
     * 
     * @return Zend\Db\Sql\Select
     */
    protected function _getDbSelect() {
        $sql = new \Zend\Db\Sql\Sql($this->_getAdapter(), $this->_tableName);
        return $sql->select();
    }

    /**
     * 开启事务
     */
    public function beginTransaction() {
        if (self::$_transactionCounter == 0) {
            $this->_getAdapter()->getDriver()->getConnection()->beginTransaction();
        }
        self::$_transactionCounter++;
    }

    /**
     * 提交事务
     */
    public function commit() {
        self::$_transactionCounter--;
        if (self::$_transactionCounter == 0) {
            $this->_getAdapter()->getDriver()->getConnection()->commit();
        }
    }

    /**
     * 回滚事务
     */
    public function rollback() {
        self::$_transactionCounter--;
        if (self::$_transactionCounter == 0) {
            $this->_getAdapter()->getDriver()->getConnection()->rollback();
        }
    }

    /**
     * 根据主键查找数据
     * 
     * @param int $id 主键
     * @return array | null
     */
    public function find($id) {
        $resultSet = $this->_getDbTableGateway()->select(array($this->_primaryKey => $id));
        $result    = $resultSet->current();
        if ($result) {
            return (array) $result;
        }
        return $result;
    }

    /**
     * 根据各个参数筛选出合适的数据
     * 
     * @param array         $columns    需要查找的字段
     * @param array         $where      筛选条件
     * @param array         $order      排序条件
     * @param int           $count      条数
     * @param int           $offset     偏移量
     * @param array         $group      分组条件
     * @return array
     */
    public function fetchAll($columns = null, $where = null, $order = null, $count = null, $offset = null, $group = null) {
        $adapter = $this->_getAdapter();
        $sql     = new \Zend\Db\Sql\Sql($adapter, $this->_tableName);
        $select  = $this->_getDbSelect();
        if ($columns) {
            $select->columns($columns);
        }
        if ($where) {
            $select->where($where);
        }
        if ($count) {
            $select->limit($count);
        }
        if ($offset) {
            $select->offset($offset);
        }
        if ($order) {
            $select->order($order);
        }
        if ($group) {
            $select->group($group);
        }
        $selectString = $sql->getSqlStringForSqlObject($select);
        $rows         = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE)->toArray();

        return $rows;
    }

    /**
     * insert data
     * 
     * @param array $data
     * @return int|false
     */
    public function insert($data, $isReturnLastInsertValue = false) {
        $dbTableGateway = $this->_getDbTableGateway();
        $result         = $dbTableGateway->insert($data);
        if ($isReturnLastInsertValue && $result) {
            return $dbTableGateway->getLastInsertValue();
        }

        return $result;
    }

    /**
     * Updates existing data.
     *
     * @param array $data
     * @param array $where
     * @return int The number of rows updated.
     */
    public function update($data, $where) {
        return $this->_getDbTableGateway()->update($data, $where);
    }

    /**
     * remove existing data.
     *
     * @param array $where
     * @return int The number of rows deleted.
     */
    public function remove($where) {
        return $this->_getDbTableGateway()->delete($where);
    }

    /**
     * 禁止clone
     */
    public function __clone() {
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }

}
